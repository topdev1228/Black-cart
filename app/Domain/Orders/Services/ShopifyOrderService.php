<?php
declare(strict_types=1);

namespace App\Domain\Orders\Services;

use App\Domain\Orders\Enums\FulfillmentOrderStatus;
use App\Domain\Orders\Enums\OrderCancelReason;
use App\Domain\Orders\Exceptions\ShopifyOrderCancellationFailedException;
use App\Domain\Orders\Exceptions\ShopifyOrderCancellationPendingException;
use App\Domain\Orders\Exceptions\ShopifyOrderCannotBeEditedException;
use App\Domain\Orders\Exceptions\ShopifyUpdatePaymentScheduleDueDateOnPaidOrderException;
use App\Domain\Orders\GraphQL\Shopify\Stable\Operations\RefundCreate;
use App\Domain\Orders\GraphQL\Shopify\Stable\Types\RefundLineItemInput;
use App\Domain\Orders\Values\Collections\ShopifyRefundLineItemInputCollection;
use App\Domain\Orders\Values\ShopifyRefundLineItemInput;
use App\Domain\Shared\Exceptions\Shopify\ShopifyMutationClientException;
use App\Domain\Shared\Exceptions\Shopify\ShopifyMutationServerException;
use App\Domain\Shared\Exceptions\Shopify\ShopifyServerException;
use App\Domain\Shared\Services\ShopifyGraphqlService;
use Brick\Money\Money;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Sleep;
use Illuminate\Support\Str;
use Log;

class ShopifyOrderService
{
    const POLLING_MAX_ATTEMPTS = 6;

    public function __construct(protected ShopifyGraphqlService $shopifyGraphqlService)
    {
    }

    public function addTags(string $orderId, array $tags): void
    {
        $queryString = <<<'QUERY'
            mutation tagsAdd ($id: ID!, $tags: [String!]!) {
              tagsAdd(
                id: $id,
                tags: $tags
              ) {
                userErrors {
                  field,
                  message
                }
              }
            }
            QUERY;

        $variables = [
            'id' => $orderId,
            'tags' => $tags,
        ];

        $this->shopifyGraphqlService->postMutation($queryString, $variables);
    }

    /**
     * @throws ShopifyOrderCancellationFailedException
     * @throws ShopifyOrderCancellationPendingException
     */
    public function cancelOrder(string $sourceOrderId, string $note = 'Authorization Failed', OrderCancelReason $storeReason = OrderCancelReason::DECLINED): void
    {
        $queryString = <<<'QUERY'
          mutation OrderCancel($orderId: ID!, $notifyCustomer: Boolean, $refund: Boolean!, $restock: Boolean!, $reason: OrderCancelReason!, $staffNote: String) {
            orderCancel(orderId: $orderId, notifyCustomer: $notifyCustomer, refund: $refund, restock: $restock, reason: $reason, staffNote: $staffNote) {
              job {
                id
                done
              }
              orderCancelUserErrors {
                field
                message
                code
              }
            }
          }
        QUERY;

        $variables = [
            'notifyCustomer' => false,
            'orderId' => $sourceOrderId,
            'reason' => $storeReason,
            'refund' => true,
            'restock' => true,
            'staffNote' => $note,
        ];

        $response = $this->shopifyGraphqlService->postMutation($queryString, $variables);
        if (!empty($response['data']['orderCancel']['orderCancelUserErrors'])) {
            throw new ShopifyOrderCancellationFailedException(message: json_encode($response['data']['orderCancel']['orderCancelUserErrors']));
        }

        if ($response['data']['orderCancel']['job']['done'] !== true) {
            $this->pollCancellationJobStatus($response['data']['orderCancel']['job']['id']);
        }
    }

    protected function pollCancellationJobStatus(string $jobId): bool
    {
        $query = /** @lang GraphQL */
            <<<'QUERY'
            query Job($id: ID!) {
                job(id: $id) {
                    done
                    id
                }
            }
            QUERY;

        $variables = [
            'id' => Str::shopifyGid($jobId, 'Job'),
        ];

        $attempts = 0;
        do {
            Sleep::for($attempts * 0.5)->seconds();
            $response = $this->shopifyGraphqlService->postMutation($query, $variables);
            $attempts++;
        } while ($attempts < static::POLLING_MAX_ATTEMPTS && $response['data']['job']['done'] !== true);

        if ($response['data']['job']['done'] !== true) {
            throw new ShopifyOrderCancellationPendingException();
        }

        return true;
    }

    public function releaseFulfillment(string $sourceId)
    {
        $queryString = /** @lang GraphQL */
            <<<'QUERY'
            query orderFulfillment($id: ID!) {
              order(id: $id) {
                id
                fulfillmentOrders(first: 10) {
                  nodes {
                    id
                    status
                  }
                }
              }
            }
            QUERY;

        $variables = [
            'id' => $sourceId,
        ];

        $response = collect($this->shopifyGraphqlService->post($queryString, $variables))->recursive();

        $queryString = /** @lang GraphQL */
            <<<'QUERY'
            mutation fulfillmentOrderReleaseHold($id: ID!) {
              fulfillmentOrderReleaseHold(id: $id) {
                fulfillmentOrder {
                  id
                  status
                }
                userErrors {
                  field
                  message
                }
              }
            }
            QUERY;

        $response->pull('data.order.fulfillmentOrders.nodes', Collection::empty())
            ->filter(fn ($fulfillmentOrder) => $fulfillmentOrder['status'] === FulfillmentOrderStatus::ON_HOLD->name)
            ->each(fn ($fulfillmentOrder) => $this->shopifyGraphqlService->postMutation($queryString, ['id' => $fulfillmentOrder['id']]));
    }

    public function addCustomLineItem(string $sourceId, Money $amount, string $title, int $quantity = 1, bool $requiresShipping = false, bool $taxable = false): ?array
    {
        $queryString = /** @lang GraphQL */
            <<<'QUERY'
            mutation orderEditAddCustomItem($calculatedOrderId: ID!, $price: MoneyInput!, $quantity: Int!, $title: String!, $requiresShipping: Boolean!, $taxable: Boolean!) {
                orderEditAddCustomItem(id: $calculatedOrderId, price: $price, quantity: $quantity, title: $title, requiresShipping: $requiresShipping, taxable: $taxable) {
                    calculatedLineItem {
                        id
                    }
                    userErrors {
                        field
                        message
                    }
                }
            }
            QUERY;

        $variables = [
            'price' => [
                'amount' => $amount->getAmount()->toFloat(),
                'currencyCode' => $amount->getCurrency()->getCurrencyCode(),
            ],
            'title' => $title,
            'quantity' => $quantity,
            'requiresShipping' => $requiresShipping,
            'taxable' => $taxable,
        ];

        $variables['calculatedOrderId'] = $this->orderEditBegin($sourceId);

        $logVariables = $variables;
        $logVariables['orderSourceId'] = $sourceId;
        Log::info('Adding custom line item to order', $logVariables);

        $this->shopifyGraphqlService->postMutation($queryString, $variables);

        return $this->orderEditCommit($variables['calculatedOrderId'], staffNote: config('shopify.order_refund_adjustment.staff_note'));
    }

    public function createRefund(
        string $sourceOrderId,
        Money $amount,
        string $note,
        ?ShopifyRefundLineItemInputCollection $refundLineItemInputs = null,
        string $gateway = 'shopify_payments',
        string $parentTransactionId = null
    ): void {
        RefundCreate::execute(
            orderId: Str::shopifyGid($sourceOrderId, 'Order'),
            note: $note,
            amount: $amount->getAmount()->toFloat(),
            gateway: $gateway,
            refundLineItems: $refundLineItemInputs?->toCollection()->map(function (ShopifyRefundLineItemInput $shopifyRefundLineItemInput) {
                return RefundLineItemInput::make(
                    lineItemId: $shopifyRefundLineItemInput->lineItemId,
                    quantity: $shopifyRefundLineItemInput->quantity,
                    locationId: $shopifyRefundLineItemInput->locationId,
                    restockType: $shopifyRefundLineItemInput->restockType->name
                );
            })->toArray(),
            currency: $amount->getCurrency()->getCurrencyCode(),
            parentTransactionId: $parentTransactionId,
        )->assertErrorFree();
    }

    protected function orderEditBegin(string $sourceId): string
    {
        $query = /** @lang GraphQL */
            <<<'QUERY'
            mutation orderEditBegin($orderId: ID!) {
                orderEditBegin(id: $orderId) {
                    calculatedOrder {
                        id
                    }
                    userErrors {
                        field
                        message
                    }
                }
            }
            QUERY;

        $variables = [
            'orderId' => $sourceId,
        ];

        try {
            $response = $this->shopifyGraphqlService->postMutation($query, $variables);
        } catch (ShopifyMutationClientException $e) {
            if ($e->getMessage() === ShopifyOrderCannotBeEditedException::MESSAGE) {
                throw new ShopifyOrderCannotBeEditedException();
            }
            throw $e;
        }

        return $response['data']['orderEditBegin']['calculatedOrder']['id'] ?? throw new ShopifyServerException();
    }

    protected function orderEditCommit(string $calculatedOrderId, bool $notifyCustomer = false, string $staffNote = ''): array
    {
        $query = /** @lang GraphQL */
            <<<'QUERY'
            mutation orderEditCommit($calculatedOrderId: ID!, $notifyCustomer: Boolean!, $staffNote: String!) {
                orderEditCommit(id: $calculatedOrderId, notifyCustomer: $notifyCustomer, staffNote: $staffNote) {
                    order {
                        id
                        lineItems(first: 100) {
                            nodes {
                                    id,
                                    product {
                                        id
                                    },
                                    variant {
                                        id
                                    }
                                    title
                                    variantTitle
                                    image {
                                        url
                                    }
                                    quantity
                                    originalUnitPriceSet {
                                        shopMoney {
                                            amount
                                            currencyCode
                                        }
                                        presentmentMoney {
                                            amount
                                            currencyCode
                                        }
                                    }
                                    originalTotalSet {
                                        shopMoney {
                                            amount
                                            currencyCode
                                        }
                                        presentmentMoney {
                                            amount
                                            currencyCode
                                        }
                                    }
                                    discountAllocations {
                                        allocatedAmountSet {
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
                                    totalDiscountSet {
                                        shopMoney {
                                            amount
                                            currencyCode
                                        }
                                        presentmentMoney {
                                            amount
                                            currencyCode
                                        }
                                    }
                                    taxLines {
                                        rate
                                        ratePercentage
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
                        }
                        }
                    }
                    userErrors {
                        field
                        message
                    }
                }
            }
            QUERY;

        $variables = [
            'calculatedOrderId' => $calculatedOrderId,
            'notifyCustomer' => $notifyCustomer,
            'staffNote' => $staffNote,
        ];

        return $this->shopifyGraphqlService->postMutation($query, $variables);
    }

    /**
     * @throws ShopifyMutationClientException
     * @throws ShopifyMutationServerException
     */
    public function updateShopifyPaymentScheduleDueDate(string $paymentTermsId, CarbonImmutable $trialExpiryDate): void
    {
        $queryString = /** @lang GraphQL */
            <<<'QUERY'
            mutation PaymentTermsUpdate($input: PaymentTermsUpdateInput!) {
                paymentTermsUpdate(input: $input) {
                    paymentTerms {
                        id
                    }
                    userErrors {
                        code
                        field
                        message
                    }
                }
            }
            QUERY;

        $variables = [
            'input' => [
                'paymentTermsId' => 'gid://shopify/PaymentTerms/' . $paymentTermsId,
                'paymentTermsAttributes' => [
                    'paymentSchedules' => [
                        [
                            'dueAt' => $trialExpiryDate->toISOString(),
                        ],
                    ],
                ],
            ],
        ];

        try {
            $this->shopifyGraphqlService->postMutation($queryString, $variables);
        } catch (ShopifyMutationClientException $e) {
            if ($e->getMessage() === ShopifyUpdatePaymentScheduleDueDateOnPaidOrderException::MESSAGE) {
                throw new ShopifyUpdatePaymentScheduleDueDateOnPaidOrderException();
            }
            throw $e;
        }
    }
}
