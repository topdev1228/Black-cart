<?php
declare(strict_types=1);

namespace App\Domain\Payments\Services;

use App\Domain\Payments\Exceptions\ShopifyMandatePaymentOutstandingAmountZeroException;
use App\Domain\Payments\Exceptions\ShopifyMandatePaymentRetryFailureLimitReachedException;
use App\Domain\Payments\Exceptions\ShopifyTransactionNotFoundException;
use App\Domain\Shared\Exceptions\Shopify\ShopifyMutationClientException;
use App\Domain\Shared\Facades\AppMetrics;
use App\Domain\Shared\Services\ShopifyGraphqlService;
use Brick\Money\Money;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Log;
use Str;

class ShopifyPaymentService
{
    public function __construct(protected ShopifyGraphqlService $shopifyGraphqlService)
    {
    }

    /**
     * If amount is null, the charge will be based on Shopify's calculations.
     *  Charging a specific amount is a feature only available on Shopify Plus stores.
     *
     * @throws ShopifyMandatePaymentOutstandingAmountZeroException
     * @throws ShopifyMandatePaymentRetryFailureLimitReachedException
     * @throws ShopifyMutationClientException
     * @throws \App\Domain\Shared\Exceptions\Shopify\ShopifyAuthenticationException
     * @throws \App\Domain\Shared\Exceptions\Shopify\ShopifyClientException
     * @throws \App\Domain\Shared\Exceptions\Shopify\ShopifyMutationServerException
     * @throws \App\Domain\Shared\Exceptions\Shopify\ShopifyQueryClientException
     * @throws \App\Domain\Shared\Exceptions\Shopify\ShopifyQueryServerException
     * @throws \App\Domain\Shared\Exceptions\Shopify\ShopifyServerException
     *
     * @version unstable
     * @see https://shopify.dev/docs/api/admin-graphql/unstable/mutations/orderCreateMandatePayment
     */
    public function createMandatePayment(string $sourceOrderId, string $paymentMandateId, bool $autoCapture = true, ?Money $amount = null): Collection
    {
        if ($amount !== null && App::context()->store->ecommercePlatformPlan !== 'shopify_plus') {
            $amount = null;
        }

        $mutation = /** @lang GraphQL */
            <<<'QUERY'
            mutation orderCreateMandatePayment($id: ID!, $idempotencyKey: String!, $mandateId: ID!, $autoCapture: Boolean, $amount: MoneyInput) {
              orderCreateMandatePayment(id: $id, idempotencyKey: $idempotencyKey, mandateId: $mandateId, autoCapture: $autoCapture, amount: $amount) {
                job {
                  id
                }
                paymentReferenceId
                userErrors {
                  field
                  message
                }
              }
            }
            QUERY;

        $variables = [
            'id' => Str::start($sourceOrderId, 'gid://shopify/Order/'),
            'idempotencyKey' => Str::replace('-', '', Str::uuid()),
            'mandateId' => $paymentMandateId,
            'autoCapture' => $autoCapture,
            'amount' => null,
        ];
        if ($amount !== null) {
            $variables['amount'] = [
                'amount' => $amount->getAmount()->toFloat(),
                'currencyCode' => $amount->getCurrency()->getCurrencyCode(),
            ];
        }

        $shopifyGraphqlService = resolve(ShopifyGraphqlService::class)->setApiVersion('unstable');

        try {
            $response = $shopifyGraphqlService->postMutation($mutation, $variables);
        } catch (ShopifyMutationClientException $e) {
            Log::error(
                'createMandatePayment failed',
                $variables,
            );

            if ($e->getMessage() === ShopifyMandatePaymentOutstandingAmountZeroException::MESSAGE) {
                throw new ShopifyMandatePaymentOutstandingAmountZeroException();
            } elseif ($e->getMessage() === ShopifyMandatePaymentRetryFailureLimitReachedException::MESSAGE) {
                throw new ShopifyMandatePaymentRetryFailureLimitReachedException();
            }
            throw $e;
        }

        if (($response['data']['orderCreateMandatePayment']['job']['id'] ?? null) === null) {
            AppMetrics::setTag('shopify.payment.error', $response);
        }

        $jobId = $response['data']['orderCreateMandatePayment']['job']['id'];
        $paymentReferenceId = $response['data']['orderCreateMandatePayment']['paymentReferenceId'];

        return collect([
            'jobId' => $jobId,
            'paymentReferenceId' => $paymentReferenceId,
        ]);
    }

    public function getOrderPaymentDetails(string $sourceOrderId): Collection
    {
        // Get the order details from Shopify
        $query = /** @lang GraphQL */
            <<<'QUERY'
            query Order($id: ID!) {
                order(id: $id) {
                    lineItems(first: 50) {
                        nodes {
                            sellingPlan {
                                sellingPlanId
                                name
                            }
                            taxable
                            taxLines {
                                title
                                rate
                                priceSet {
                                    shopMoney {
                                        amount
                                        currencyCode
                                    }
                                    presentmentMoney {
                                        amount
                                        currencyCode
                                    }
                                }
                            }
                            discountedTotalSet {
                                shopMoney {
                                    amount
                                    currencyCode
                                }
                                presentmentMoney {
                                    amount
                                    currencyCode
                                }
                            }
                        }
                    }
                    paymentCollectionDetails {
                        vaultedPaymentMethods {
                            id
                        }
                    }
                    paymentTerms {
                        id
                        paymentSchedules(first: 10) {
                            nodes {
                                id
                                completedAt
                                presentmentMoney: amount  {
                                    amount
                                    currencyCode
                                }
                            }
                        }
                    }
                }
            }
            QUERY;

        $variables = [
            'id' => Str::start($sourceOrderId, 'gid://shopify/Order/'),
        ];

        return collect($this->shopifyGraphqlService->post($query, $variables))->recursive();
    }

    /**
     * @return Collection{done: string, status: string, errorMessage: string|null}
     */
    public function getPaymentAttemptJob(string $jobId, string $paymentReferenceId, string $sourceOrderId): Collection
    {
        $query = /** @lang GraphQL */
            <<<'QUERY'
            query OrderPaymentAttemptJob($id: ID! $paymentReferenceId: String! $orderId: ID!) {
              job(id: $id) {
                id
                done
                query {
                  orderPaymentStatus(
                    paymentReferenceId: $paymentReferenceId
                    orderId: $orderId
                  ) {
                    status
                    translatedErrorMessage
                  }
                }
              }
            }
            QUERY;

        $variables = [
            'id' => $jobId,
            'paymentReferenceId' => $paymentReferenceId,
            'orderId' => $sourceOrderId,
        ];

        $response = $this->shopifyGraphqlService->post($query, $variables);

        return collect([
            'done' => $response['data']['job']['done'],
            'status' => $response['data']['job']['query']['orderPaymentStatus']['status'],
            'errorMessage' => $response['data']['job']['query']['orderPaymentStatus']['translatedErrorMessage'] ?? null,
        ]);
    }

    public function getTransaction(string $sourceOrderId, string $sourceTransactionId): Collection
    {
        $query = /** @lang GraphQL */
            <<<'QUERY'
            query Order($id: ID!) {
                order(id: $id) {
                    transactions {
                        amountSet {
                            shopMoney {
                                amount
                                currencyCode
                            }
                            presentmentMoney {
                                amount
                                currencyCode
                            }
                        }
                        totalUnsettledSet {
                            shopMoney {
                                amount
                                currencyCode
                            }
                            presentmentMoney {
                                amount
                                currencyCode
                            }
                        }
                        authorizationExpiresAt
                        createdAt
                        manuallyCapturable
                        id
                        kind
                        status
                        gateway
                        test
                        paymentId
                    }
                }
            }
            QUERY;

        $variables = [
            'id' => Str::shopifyGid($sourceOrderId, 'Order'),
        ];

        $response = collect($this->shopifyGraphqlService->post($query, $variables))->recursive();
        /** @var Collection $transactions */
        $transactions = $response->pull('data.order.transactions');

        return $transactions->firstOrFail('id', Str::shopifyGid($sourceTransactionId, 'OrderTransaction'));
    }

    public function capturePayment(string $sourceOrderId, string $sourceTransactionId, Money $amount): Collection
    {
        $mutation = /** @lang GraphQL */
            <<<'QUERY'
            mutation orderCapturePayment($orderId: ID!, $transactionId: ID!, $amount: Money!, $currency: CurrencyCode!) {
                orderCapture(input: {
                    id: $orderId
                    amount: $amount
                    currency: $currency
                    parentTransactionId: $transactionId
                }) {
                    userErrors {
                        field
                        message
                    }
                    transaction {
                        id
                        createdAt
                        status
                        kind
                        accountNumber
                        amountSet {
                            shopMoney {
                                amount
                                currencyCode
                            }
                            presentmentMoney {
                                amount
                                currencyCode
                            }
                        }
                    }
                }
            }
            QUERY;

        $variables = [
            'orderId' => Str::shopifyGid($sourceOrderId, 'Order'),
            'transactionId' => Str::shopifyGid($sourceTransactionId, 'OrderTransaction'),
            'amount' => $amount->getAmount()->toFloat(),
            'currency' => $amount->getCurrency()->getCurrencyCode(),
        ];

        $response = $this->shopifyGraphqlService->post($mutation, $variables);

        return collect($response)->recursive()->pull('data.orderCapture.transaction') ?? throw new ShopifyTransactionNotFoundException;
    }
}
