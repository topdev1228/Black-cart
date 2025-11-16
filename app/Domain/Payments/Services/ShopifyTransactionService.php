<?php
declare(strict_types=1);

namespace App\Domain\Payments\Services;

use App\Domain\Payments\Enums\PaymentStatus;
use App\Domain\Payments\Exceptions\ShopifyTransactionNotFoundException;
use App\Domain\Shared\Services\ShopifyGraphqlService;
use Illuminate\Support\Collection;
use Illuminate\Support\Sleep;
use Str;

class ShopifyTransactionService
{
    const POLLING_MAX_ATTEMPTS = 10;

    public function __construct(protected ShopifyGraphqlService $shopifyGraphqlService)
    {
    }

    public function getTransactionsFromJob(string $jobId, string $sourceOrderId): Collection
    {
        $query = /** @lang GraphQL */
            <<<'QUERY'
            query Job($id: ID!, $orderId: ID!) {
                job(id: $id) {
                    done
                    id
                    query {
                        order(id: $orderId) {
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
                                receiptJson
                            }
                        }
                    }
                }
            }
            QUERY;

        $variables = [
            'id' => Str::shopifyGid($jobId, 'Job'),
            'orderId' => Str::shopifyGid($sourceOrderId, 'Order'),
        ];

        $attempts = 0;
        do {
            $attempts++;
            Sleep::for($attempts * 0.5)->seconds();
            $response = $this->shopifyGraphqlService->post($query, $variables);
        } while ($attempts < static::POLLING_MAX_ATTEMPTS && $response['data']['job']['done'] !== true);

        if (!array_key_exists('query', $response['data']['job']) || empty($response['data']['job']['query'])) {
            return Collection::empty();
        }

        if ($response['data']['job']['done'] !== true) {
            throw new ShopifyTransactionNotFoundException("Transaction job {$jobId} for order {$sourceOrderId} timed out.");
        }

        if (($response['data']['job']['query']['orderPaymentStatus']['status'] ?? null) === PaymentStatus::ERROR->value) {
            throw new ShopifyTransactionNotFoundException($response['data']['job']['query']['orderPaymentStatus']['translatedErrorMessage']);
        }

        return collect($response['data']['job']['query']['order']['transactions'])->recursive();
    }

    public function getTransactionByIdAndOrderId(string $sourceTransactionId, string $sourceOrderId): Collection
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
                        receiptJson
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
}
