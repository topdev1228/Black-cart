<?php

declare(strict_types=1);

namespace App\Domain\Orders\Services;

use App;
use App\Domain\Orders\Enums\LineItemDecisionStatus;
use App\Domain\Orders\Enums\LineItemStatus;
use App\Domain\Orders\Enums\LineItemStatusUpdatedBy;
use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Orders\Enums\ShopifyRefundLineItemRestockType;
use App\Domain\Orders\Enums\TransactionKind;
use App\Domain\Orders\Enums\TransactionStatus;
use App\Domain\Orders\Events\OrderCompletedEvent;
use App\Domain\Orders\Events\PaymentRequiredEvent;
use App\Domain\Orders\Events\TrialableDeliveredEvent;
use App\Domain\Orders\Exceptions\ShopifyOrderCancellationFailedException;
use App\Domain\Orders\Exceptions\ShopifyOrderCancellationPendingException;
use App\Domain\Orders\Exceptions\ShopifyOrderCannotBeEditedException;
use App\Domain\Orders\Exceptions\ShopifyUpdatePaymentScheduleDueDateOnPaidOrderException;
use App\Domain\Orders\Mail\AssumedDeliveryMerchantReminder;
use App\Domain\Orders\Mail\OrderConfirmation;
use App\Domain\Orders\Mail\OrderDelivered;
use App\Domain\Orders\Repositories\OrderRepository;
use App\Domain\Orders\Repositories\TrialGroupRepository;
use App\Domain\Orders\Values\Collections\LineItemCollection;
use App\Domain\Orders\Values\Collections\OrderCollection;
use App\Domain\Orders\Values\Collections\ShopifyRefundLineItemInputCollection;
use App\Domain\Orders\Values\LineItem;
use App\Domain\Orders\Values\Order as OrderValue;
use App\Domain\Orders\Values\Refund;
use App\Domain\Orders\Values\ShopifyRefundLineItemInput;
use App\Domain\Orders\Values\WebhookPaymentSchedulesDue;
use App\Domain\Shared\Exceptions\Shopify\ShopifyAuthenticationException;
use App\Domain\Shared\Exceptions\Shopify\ShopifyClientException;
use App\Domain\Shared\Exceptions\Shopify\ShopifyServerException;
use App\Domain\Shared\Services\ShopifyGraphqlService;
use App\Domain\Shopify\Exceptions\InternalShopifyRequestException;
use Arr;
use Brick\Money\Money;
use Carbon\CarbonImmutable;
use Exception;
use Feature;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;

class OrderService
{
    public function __construct(
        protected OrderRepository $orderRepository,
        protected LineItemService $lineItemService,
        protected ShopifyOrderService $shopifyOrderService,
        protected ShopifyGraphqlService $shopifyGraphqlService,
        protected TransactionService $transactionService,
        protected TrialGroupRepository $trialGroupRepository,
    ) {
    }

    public function getById(string $id): OrderValue
    {
        return $this->orderRepository->getById($id);
    }

    /**
     * Returns Order by id without guarding for currentStore
     */
    public function getUnsafeById(string $id): OrderValue
    {
        return $this->orderRepository->getById($id, false);
    }

    public function getByTrialGroupId(string $trialGroupId): OrderValue
    {
        return $this->orderRepository->getByTrialGroupId($trialGroupId);
    }

    public function getBySourceId(string $sourceId): OrderValue
    {
        return $this->orderRepository->getBySourceId($sourceId);
    }

    public function create(OrderValue $order): OrderValue
    {
        return $this->orderRepository->create($order);
    }

    public function update(OrderValue $order): OrderValue
    {
        return $this->orderRepository->update($order);
    }

    public function all(): OrderCollection
    {
        return $this->orderRepository->all();
    }

    public function cancelOrder(OrderValue $order, bool $updateSource = false): OrderValue
    {
        if ($updateSource) {
            try {
                $this->shopifyOrderService->cancelOrder($order->sourceId);
            } catch (ShopifyOrderCancellationPendingException|ShopifyOrderCancellationFailedException $e) {
                Log::warning('[OrderService] Order failed to Cancel on Shopify:' . $e->getMessage());

                return $order;
            }
        }

        $order->status = OrderStatus::CANCELLED;

        return $this->update($order);
    }

    // @deprecated - this is only meant for quick and dirty feature request from Shopify.  Don't use this method.
    public function endTrialBeforeExpiry(string $id): void
    {
        $order = $this->getById($id);

        // Using the order ID as the trial group ID for now
        PaymentRequiredEvent::dispatch($order->id, $order->sourceId, $order->id, $order->outstandingCustomerAmount);
    }

    public function recalculateOrderTotals(string $id): OrderValue
    {
        $orderValue = $this->getById($id);
        $tbybRefundGrossSalesShopAmount = Money::ofMinor(0, $orderValue->shopCurrency->value);
        $tbybRefundGrossSalesCustomerAmount = Money::ofMinor(0, $orderValue->customerCurrency->value);
        $upfrontRefundGrossSalesShopAmount = Money::ofMinor(0, $orderValue->shopCurrency->value);
        $upfrontRefundGrossSalesCustomerAmount = Money::ofMinor(0, $orderValue->customerCurrency->value);
        $tbybRefundDiscountsShopAmount = Money::ofMinor(0, $orderValue->shopCurrency->value);
        $tbybRefundDiscountsCustomerAmount = Money::ofMinor(0, $orderValue->customerCurrency->value);
        $upfrontRefundDiscountsShopAmount = Money::ofMinor(0, $orderValue->shopCurrency->value);
        $upfrontRefundDiscountsCustomerAmount = Money::ofMinor(0, $orderValue->customerCurrency->value);
        $totalOrderLevelRefundsShopAmount = Money::ofMinor(0, $orderValue->shopCurrency->value);
        $totalOrderLevelRefundsCustomerAmount = Money::ofMinor(0, $orderValue->customerCurrency->value);
        $tbybTotalRefundsShopAmount = Money::ofMinor(0, $orderValue->shopCurrency->value);
        $tbybTotalRefundsCustomerAmount = Money::ofMinor(0, $orderValue->customerCurrency->value);
        $upfrontTotalRefundsShopAmount = Money::ofMinor(0, $orderValue->shopCurrency->value);
        $upfrontTotalRefundsCustomerAmount = Money::ofMinor(0, $orderValue->customerCurrency->value);
        $totalRefundLineItemShopAmount = Money::ofMinor(0, $orderValue->shopCurrency->value);
        $totalRefundLineItemCustomerAmount = Money::ofMinor(0, $orderValue->customerCurrency->value);
        $refundedShopAmount = Money::ofMinor(0, $orderValue->shopCurrency->value);
        $refundedCustomerAmount = Money::ofMinor(0, $orderValue->customerCurrency->value);

        /** @var Refund $refundValue */
        foreach ($orderValue->refunds as $refundValue) {
            $tbybRefundGrossSalesShopAmount = $tbybRefundGrossSalesShopAmount->plus($refundValue->tbybGrossSalesShopAmount);
            $tbybRefundGrossSalesCustomerAmount = $tbybRefundGrossSalesCustomerAmount->plus($refundValue->tbybGrossSalesCustomerAmount);
            $upfrontRefundGrossSalesShopAmount = $upfrontRefundGrossSalesShopAmount->plus($refundValue->upfrontGrossSalesShopAmount);
            $upfrontRefundGrossSalesCustomerAmount = $upfrontRefundGrossSalesCustomerAmount->plus($refundValue->upfrontGrossSalesCustomerAmount);
            $tbybRefundDiscountsShopAmount = $tbybRefundDiscountsShopAmount->plus($refundValue->tbybDiscountsShopAmount);
            $tbybRefundDiscountsCustomerAmount = $tbybRefundDiscountsCustomerAmount->plus($refundValue->tbybDiscountsCustomerAmount);
            $upfrontRefundDiscountsShopAmount = $upfrontRefundDiscountsShopAmount->plus($refundValue->upfrontDiscountsShopAmount);
            $upfrontRefundDiscountsCustomerAmount = $upfrontRefundDiscountsCustomerAmount->plus($refundValue->upfrontDiscountsCustomerAmount);
            $totalOrderLevelRefundsShopAmount = $totalOrderLevelRefundsShopAmount->plus($refundValue->orderLevelRefundShopAmount);
            $totalOrderLevelRefundsCustomerAmount = $totalOrderLevelRefundsCustomerAmount->plus($refundValue->orderLevelRefundCustomerAmount);
            $tbybTotalRefundsShopAmount = $tbybTotalRefundsShopAmount->plus($refundValue->tbybTotalShopAmount);
            $tbybTotalRefundsCustomerAmount = $tbybTotalRefundsCustomerAmount->plus($refundValue->tbybTotalCustomerAmount);
            $upfrontTotalRefundsShopAmount = $upfrontTotalRefundsShopAmount->plus($refundValue->upfrontTotalShopAmount);
            $upfrontTotalRefundsCustomerAmount = $upfrontTotalRefundsCustomerAmount->plus($refundValue->upfrontTotalCustomerAmount);
            $totalRefundLineItemShopAmount = $totalRefundLineItemShopAmount->plus($refundValue->tbybTotalShopAmount)->plus($refundValue->upfrontTotalShopAmount);
            $totalRefundLineItemCustomerAmount = $totalRefundLineItemCustomerAmount->plus($refundValue->tbybTotalCustomerAmount)->plus($refundValue->upfrontTotalCustomerAmount);
            $refundedCustomerAmount = $refundedCustomerAmount->plus($refundValue->refundedCustomerAmount);
            $refundedShopAmount = $refundedShopAmount->plus($refundValue->refundedShopAmount);
        }

        $orderValue->tbybRefundGrossSalesShopAmount = $tbybRefundGrossSalesShopAmount;
        $orderValue->tbybRefundGrossSalesCustomerAmount = $tbybRefundGrossSalesCustomerAmount;
        $orderValue->upfrontRefundGrossSalesShopAmount = $upfrontRefundGrossSalesShopAmount;
        $orderValue->upfrontRefundGrossSalesCustomerAmount = $upfrontRefundGrossSalesCustomerAmount;
        $orderValue->tbybRefundDiscountsShopAmount = $tbybRefundDiscountsShopAmount;
        $orderValue->tbybRefundDiscountsCustomerAmount = $tbybRefundDiscountsCustomerAmount;
        $orderValue->upfrontRefundDiscountsShopAmount = $upfrontRefundDiscountsShopAmount;
        $orderValue->upfrontRefundDiscountsCustomerAmount = $upfrontRefundDiscountsCustomerAmount;
        $orderValue->totalOrderLevelRefundsShopAmount = $totalOrderLevelRefundsShopAmount;
        $orderValue->totalOrderLevelRefundsCustomerAmount = $totalOrderLevelRefundsCustomerAmount;

        $orderValue->tbybNetSalesShopAmount = $orderValue->originalTbybGrossSalesShopAmount
            ->minus($orderValue->originalTbybDiscountsShopAmount)->minus($tbybRefundGrossSalesShopAmount)
            ->plus($tbybRefundDiscountsShopAmount);
        $orderValue->tbybNetSalesCustomerAmount = $orderValue->originalTbybGrossSalesCustomerAmount
            ->minus($orderValue->originalTbybDiscountsCustomerAmount)->minus($tbybRefundGrossSalesCustomerAmount)
            ->plus($tbybRefundDiscountsCustomerAmount);

        $orderValue->upfrontNetSalesShopAmount = $orderValue->originalUpfrontGrossSalesShopAmount
            ->minus($orderValue->originalUpfrontDiscountsShopAmount)->minus($upfrontRefundGrossSalesShopAmount)
            ->plus($upfrontRefundDiscountsShopAmount);
        $orderValue->upfrontNetSalesCustomerAmount = $orderValue->originalUpfrontGrossSalesCustomerAmount
            ->minus($orderValue->originalUpfrontDiscountsCustomerAmount)->minus($upfrontRefundGrossSalesCustomerAmount)
            ->plus($upfrontRefundDiscountsCustomerAmount);

        $orderValue->totalNetSalesShopAmount = $orderValue->tbybNetSalesShopAmount
            ->plus($orderValue->upfrontNetSalesShopAmount);
        $orderValue->totalNetSalesCustomerAmount = $orderValue->tbybNetSalesCustomerAmount
            ->plus($orderValue->upfrontNetSalesCustomerAmount);

        $transactionShopAmount = Money::zero($orderValue->shopCurrency->value);
        $transactionCustomerAmount = Money::zero($orderValue->customerCurrency->value);
        foreach ($orderValue->transactions as $transaction) {
            if (!in_array($transaction->kind, [TransactionKind::SALE, TransactionKind::CAPTURE])) {
                continue;
            }
            if ($transaction->status !== TransactionStatus::SUCCESS) {
                continue;
            }
            $transactionShopAmount = $transactionShopAmount->plus($transaction->shopAmount);
            $transactionCustomerAmount = $transactionCustomerAmount->plus($transaction->customerAmount);
        }

        // Order outstanding amount = (original order total) minus (the sum of all sale or capture transactions)
        //  minus (the sum of all order level refunds) minus (the sum of all TBYB refunds after taxes and discounts)
        // Upfront line item refunds are excluded because they have already been charged and are therefore, not a part
        //  of the outstanding amount calculations.

        $orderValue->outstandingShopAmount = $orderValue->totalShopAmount
            ->minus($transactionShopAmount)->minus($totalRefundLineItemShopAmount)
            ->plus($refundedShopAmount);
        $orderValue->outstandingCustomerAmount = $orderValue->totalCustomerAmount
            ->minus($transactionCustomerAmount)->minus($totalRefundLineItemCustomerAmount)
            ->plus($refundedCustomerAmount);

        $updatedOrderValue = $this->update($orderValue);

        if ($orderValue->outstandingShopAmount->isLessThanOrEqualTo(0) ||
            $orderValue->outstandingCustomerAmount->isLessThanOrEqualTo(0)) {
            $updatedOrderValue = $this->completeOrder($orderValue->id);
        }

        return $updatedOrderValue;
    }

    public function completeOrder(string $id): OrderValue
    {
        $order = $this->getById($id);

        $order->status = OrderStatus::COMPLETED;
        $order->completedAt = Date::now();
        $updatedOrderValue = $this->update($order);

        OrderCompletedEvent::dispatch($order);

        return $updatedOrderValue;
    }

    protected function fetchSellingPlans(string $orderGid): array
    {
        $query = <<<QUERY
            query {
              order(id: "$orderGid"){
              lineItems (first: 50) {
                edges {
                node {
                  id,
                  sellingPlan {
                    sellingPlanId,
                    name
                  }
                }
                  }
                    },
                    paymentTerms {
                      id,
                      paymentTermsName
                  }
               }
            }
        QUERY;

        $sellingPlanData = $this->shopifyGraphqlService->post($query);

        return $sellingPlanData['data']['order']['lineItems']['edges'];
    }

    public function parseWebhookCreateData(Collection $shopData, string $storeId): OrderValue
    {
        $lineItemSellingPlanData = $this->fetchSellingPlans($shopData['admin_graphql_api_id']);

        /** @var LineItemCollection<LineItem> $lineItemValues */
        $lineItemValues = $this->lineItemService->generateFromOrderData($shopData, $shopData['admin_graphql_api_id']);

        $shopCurrency = CurrencyAlpha3::from($shopData['currency'] ?? 'USD');
        $customerCurrency = CurrencyAlpha3::from($shopData['payment_terms']['payment_schedules'][0]['currency'] ?? 'USD');

        $calculatedTotals = $this->calculateTotalData($lineItemValues, $shopCurrency, $customerCurrency);

        return OrderValue::from([
            'storeId' => $storeId,
            'id' => null,
            'sourceId' => (string) $shopData['admin_graphql_api_id'],
            'status' => OrderStatus::OPEN,
            'orderData' => $shopData->toArray(),
            'blackcartMetadata' => [
                'line_item_selling_plans' => $lineItemSellingPlanData,
            ],
            'lineItems' => $lineItemValues,

            'name' => $shopData['name'],
            'taxes_included' => $shopData['taxes_included'] ?? false,
            'taxes_exempt' => $shopData['tax_exempt'] ?? false,
            'tags' => $shopData['tags'] ?? '',
            'discount_codes' => $shopData['discount_codes'] ? json_encode($shopData['discount_codes']) : '{}',
            'test' => false,
            'payment_terms_id' => $shopData['payment_terms'] ? $shopData['payment_terms']['id'] : null,
            'payment_terms_name' => $shopData['payment_terms'] ? $shopData['payment_terms']['payment_terms_name'] : null,
            'payment_terms_type' => $shopData['payment_terms'] ? $shopData['payment_terms']['payment_terms_type'] : null,
            'shop_currency' => $shopCurrency,
            'customer_currency' => $customerCurrency,
            'total_shop_amount' => Money::of($shopData['total_price_set']['shop_money']['amount'] ?? 0, $shopCurrency->value),
            'total_customer_amount' => Money::of($shopData['total_price_set']['presentment_money']['amount'] ?? 0, $customerCurrency->value),
            'original_outstanding_shop_amount' => Money::of($shopData['total_outstanding'] ?? 0, $shopCurrency->value),
            'original_outstanding_customer_amount' => Money::of($shopData['payment_terms']['payment_schedules'][0]['amount'] ?? 0, $customerCurrency->value),
            'outstanding_shop_amount' => Money::of($shopData['total_outstanding'] ?? 0, $shopCurrency->value),
            'outstanding_customer_amount' => Money::of($shopData['payment_terms']['payment_schedules'][0]['amount'] ?? 0, $customerCurrency->value),
        ] + $calculatedTotals);
    }

    protected function calculateTotalData(LineItemCollection $lineItems, CurrencyAlpha3 $shopCurrency, CurrencyAlpha3 $customerCurrency): array
    {
        $calculated = $lineItems->reduce(function ($carry, LineItem $lineItem) {
            return [
                'shopTbybGross' => $carry['shopTbybGross']->plus($lineItem->isTbyb ? $lineItem->totalPriceShopAmount : 0),
                'shopUpfrontGross' => $carry['shopUpfrontGross']->plus($lineItem->isTbyb ? 0 : $lineItem->totalPriceShopAmount),
                'shopTbybDiscount' => $carry['shopTbybDiscount']->plus($lineItem->isTbyb ? $lineItem->discountShopAmount : 0),
                'shopUpfrontDiscount' => $carry['shopUpfrontDiscount']->plus($lineItem->isTbyb ? 0 : $lineItem->discountShopAmount),
                'shopDiscountTotal' => $carry['shopDiscountTotal']->plus($lineItem->discountShopAmount),
                'shopTotal' => $carry['shopTotal']->plus($lineItem->totalPriceShopAmount),
                'customerTbybGross' => $carry['customerTbybGross']->plus($lineItem->isTbyb ? $lineItem->totalPriceCustomerAmount : 0),
                'customerUpfrontGross' => $carry['customerUpfrontGross']->plus($lineItem->isTbyb ? 0 : $lineItem->totalPriceCustomerAmount),
                'customerTbybDiscount' => $carry['customerTbybDiscount']->plus($lineItem->isTbyb ? $lineItem->discountCustomerAmount : 0),
                'customerUpfrontDiscount' => $carry['customerUpfrontDiscount']->plus($lineItem->isTbyb ? 0 : $lineItem->discountCustomerAmount),
                'customerDiscountTotal' => $carry['customerDiscountTotal']->plus($lineItem->discountCustomerAmount),
                'customerTotal' => $carry['customerTotal']->plus($lineItem->totalPriceCustomerAmount),
            ];
        }, [
            'shopTbybGross' => Money::of(0, $shopCurrency->value),
            'shopUpfrontGross' => Money::of(0, $shopCurrency->value),
            'shopTbybDiscount' => Money::of(0, $shopCurrency->value),
            'shopUpfrontDiscount' => Money::of(0, $shopCurrency->value),
            'shopDiscountTotal' => Money::of(0, $shopCurrency->value),
            'shopTotal' => Money::of(0, $shopCurrency->value),

            'customerTbybGross' => Money::of(0, $customerCurrency->value),
            'customerUpfrontGross' => Money::of(0, $customerCurrency->value),
            'customerTbybDiscount' => Money::of(0, $customerCurrency->value),
            'customerUpfrontDiscount' => Money::of(0, $customerCurrency->value),
            'customerDiscountTotal' => Money::of(0, $customerCurrency->value),
            'customerTotal' => Money::of(0, $customerCurrency->value),
        ]);

        return [
            'original_tbyb_gross_sales_shop_amount' => $calculated['shopTbybGross'],
            'original_tbyb_gross_sales_customer_amount' => $calculated['customerTbybGross'],
            'original_upfront_gross_sales_shop_amount' => $calculated['shopUpfrontGross'],
            'original_upfront_gross_sales_customer_amount' => $calculated['customerUpfrontGross'],
            'original_total_gross_sales_shop_amount' => $calculated['shopTotal'],
            'original_total_gross_sales_customer_amount' => $calculated['customerTotal'],
            'original_tbyb_discounts_shop_amount' => $calculated['shopTbybDiscount'],
            'original_tbyb_discounts_customer_amount' => $calculated['customerTbybDiscount'],
            'original_upfront_discounts_shop_amount' => $calculated['shopUpfrontDiscount'],
            'original_upfront_discounts_customer_amount' => $calculated['customerUpfrontDiscount'],
            'original_total_discounts_shop_amount' => $calculated['shopDiscountTotal'],
            'original_total_discounts_customer_amount' => $calculated['customerDiscountTotal'],

            'tbyb_refund_gross_sales_shop_amount' => Money::of(0, $shopCurrency->value),
            'tbyb_refund_gross_sales_customer_amount' => Money::of(0, $customerCurrency->value),
            'upfront_refund_gross_sales_shop_amount' => Money::of(0, $shopCurrency->value),
            'upfront_refund_gross_sales_customer_amount' => Money::of(0, $customerCurrency->value),
            'total_order_level_refunds_shop_amount' => Money::of(0, $shopCurrency->value),
            'total_order_level_refunds_customer_amount' => Money::of(0, $customerCurrency->value),
            'tbyb_refund_discounts_shop_amount' => Money::of(0, $shopCurrency->value),
            'tbyb_refund_discounts_customer_amount' => Money::of(0, $customerCurrency->value),
            'upfront_refund_discounts_shop_amount' => Money::of(0, $shopCurrency->value),
            'upfront_refund_discounts_customer_amount' => Money::of(0, $customerCurrency->value),

            'tbyb_net_sales_shop_amount' => $calculated['shopTbybGross']->minus($calculated['shopTbybDiscount']),
            'tbyb_net_sales_customer_amount' => $calculated['customerTbybGross']->minus($calculated['customerTbybDiscount']),
            'upfront_net_sales_shop_amount' => $calculated['shopUpfrontGross']->minus($calculated['shopUpfrontDiscount']),
            'upfront_net_sales_customer_amount' => $calculated['customerUpfrontGross']->minus($calculated['customerUpfrontDiscount']),
            'total_net_sales_shop_amount' => $calculated['shopTotal']->minus($calculated['shopDiscountTotal']),
            'total_net_sales_customer_amount' => $calculated['customerTotal']->minus($calculated['customerDiscountTotal']),
        ];
    }

    public function getStoreIdsByDate(CarbonImmutable $cutoff): array
    {
        return $this->orderRepository->getStoreIdsByDate($cutoff);
    }

    public function getGrossSales(CarbonImmutable $endDate, ?CarbonImmutable $startDate = null): string
    {
        return $this->orderRepository->getGrossSales($endDate, $startDate);
    }

    public function getTotalDiscounts(CarbonImmutable $endDate, ?CarbonImmutable $startDate = null): string
    {
        return $this->orderRepository->getTotalDiscounts($endDate, $startDate);
    }

    public function getShopCurrency(): CurrencyAlpha3
    {
        return $this->orderRepository->getShopCurrency();
    }

    public function addBlackcartTagsToOrder(OrderValue $order): void
    {
        $this->addTags($order, ['blackcart']);
    }

    public function addCompleteTagsToOrder(OrderValue $order): void
    {
        $this->addTags($order, ['blackcart-complete']);
    }

    public function addTags(OrderValue $order, array $tags): void
    {
        // This will throw not found exception if order does not exist
        $this->getById($order->id);

        try {
            $this->shopifyOrderService->addTags($order->sourceId, $tags);
        } catch (ShopifyClientException|ShopifyServerException|ShopifyAuthenticationException $e) {
            throw new InternalShopifyRequestException(
                __('Internal call to Shopify failed, please try again in a few minutes.'),
                $e,
            );
        }
    }

    public function releaseFulfillment(string $orderId): void
    {
        $order = $this->orderRepository->getById($orderId);
        $this->shopifyOrderService->releaseFulfillment($order->sourceId);
    }

    public function sendAssumedDeliveryMerchantNotification(string $orderId): void
    {
        try {
            $order = $this->getById($orderId);
        } catch (ModelNotFoundException $e) {
            return;
        }

        if (
            $order->assumedDeliveryMerchantEmailSentAt !== null ||
            $order->status !== OrderStatus::OPEN ||
            empty($order->lineItems) ||
            !$this->areAllLineItemsInOpenStatus($order)
        ) {
            return;
        }

        $merchantEmail = $this->getMerchantEmail();

        if ($merchantEmail) {
            Mail::to($merchantEmail)->send(new AssumedDeliveryMerchantReminder($order, $merchantEmail));

            $order->assumedDeliveryMerchantEmailSentAt = Date::now();
            $this->update($order);
        }
    }

    public function getMerchantEmail(): ?string
    {
        $returnResponse = Http::get('http://localhost:8080/api/stores/settings');
        $email = Arr::get($returnResponse, 'settings.customerSupportEmail.value', null);

        return $email ? $email : null;
    }

    protected function areAllLineItemsInOpenStatus(OrderValue $order): bool
    {
        foreach ($order->lineItems as $lineItem) {
            if ($lineItem->status !== LineItemStatus::OPEN) {
                return false;
            }
        }

        return true;
    }

    public function assumeDelivered(string $orderId): void
    {
        try {
            $order = $this->getById($orderId);
        } catch (ModelNotFoundException $e) {
            return;
        }

        if (
            $order->status !== OrderStatus::OPEN ||
            empty($order->lineItems) ||
            !$this->areAllLineItemsInOpenStatus($order)
        ) {
            return;
        }

        foreach ($order->lineItems as $lineItem) {
            $lineItem->status = LineItemStatus::DELIVERED;
            $lineItem->statusUpdatedBy = LineItemStatusUpdatedBy::ASSUMED_DELIVERY;

            $this->lineItemService->save($lineItem);

            TrialableDeliveredEvent::dispatch($lineItem->id, TrialService::TRIAL_SOURCE_KEY);
        }
    }

    public function addOrderRefundAdjustment(string $orderId, Money $amount): bool
    {
        if (Feature::enabled('shopify-perm-b-kill-fix-shopify-outstanding-balance-adjustments')) {
            return true;
        }

        if ($amount->isNegativeOrZero()) {
            return true;
        }

        $order = $this->orderRepository->getById($orderId);

        try {
            $result = $this->shopifyOrderService->addCustomLineItem($order->sourceId, $amount, config('shopify.order_refund_adjustment.line_item_title'));
        } catch (ShopifyOrderCannotBeEditedException $e) {
            Log::error(
                'addCustomLineItem failed, ShopifyOrderCannotBeEditedException thrown',
                [
                    'orderId' => $order->id,
                    'amount' => $amount->getAmount()->toFloat(),
                    'error' => $e->getMessage(),
                ],
            );

            return false;
        } catch (Exception $e) {
            Log::error(
                'addCustomLineItem failed',
                [
                    'orderId' => $order->id,
                    'amount' => $amount->getAmount()->toFloat(),
                    'error' => $e->getMessage(),
                ],
            );
            throw $e;
        }

        if (!$result) {
            return false;
        }

        $lineItemData = collect($result)
            ->recursive()
            ->pull('data.orderEditCommit.order.lineItems.nodes')
            ?->where('title', config('shopify.order_refund_adjustment.line_item_title'))
            ?->last();

        if ($lineItemData === null) {
            return false;
        }

        $this->lineItemService->save(LineItem::from([
            'order_id' => $order->id,
            'source_order_id' => $order->sourceId,
            'source_id' => $lineItemData['id'],
            'product_title' => $lineItemData['title'],
            'quantity' => 1,
            'line_item_data' => $lineItemData->toArray(),
            'shop_currency' => $order->shopCurrency,
            'customer_currency' => $order->customerCurrency,
            'price_shop_amount' => Money::of($lineItemData['originalUnitPriceSet']['shopMoney']['amount'], $order->shopCurrency->value),
            'price_customer_amount' => Money::of($lineItemData['originalUnitPriceSet']['presentmentMoney']['amount'], $order->customerCurrency->value),
            'total_price_shop_amount' => Money::of($lineItemData['originalTotalSet']['shopMoney']['amount'], $order->shopCurrency->value),
            'total_price_customer_amount' => Money::of($lineItemData['originalTotalSet']['presentmentMoney']['amount'], $order->customerCurrency->value),
            'discount_shop_amount' => 0,
            'discount_customer_amount' => 0,
            'tax_shop_amount' => 0,
            'tax_customer_amount' => 0,
            'status' => LineItemStatus::INTERNAL,
            'decision_status' => LineItemDecisionStatus::INTERNAL,
        ]));

        return true;
    }

    public function refundOrderRefundAdjustments(string $orderId): void
    {
        if (Feature::enabled('shopify-perm-b-kill-fix-shopify-outstanding-balance-adjustments')) {
            return;
        }

        $order = $this->orderRepository->getById($orderId);

        $lineItems = $this->lineItemService->getByStatus($orderId, LineItemStatus::INTERNAL)->toCollection();

        if ($lineItems->isEmpty()) {
            return;
        }

        /** @var ShopifyRefundLineItemInputCollection $refundLineItems */
        $refundLineItems = ShopifyRefundLineItemInput::collection($lineItems->map(function (LineItem $lineItem) {
            return ShopifyRefundLineItemInput::from([
                'lineItemId' => $lineItem->sourceId,
                'restock_type' => ShopifyRefundLineItemRestockType::NO_RESTOCK,
                'quantity' => 1,
            ]);
        })->toArray());

        $transaction = $this->transactionService->getLatestTransaction($orderId, [TransactionKind::SALE, TransactionKind::CAPTURE]);

        $this->shopifyOrderService->createRefund(
            sourceOrderId: Str::shopifyGid($order->sourceId, 'Order'),
            amount: Money::zero($order->customerCurrency->value),
            note: config('shopify.order_refund_adjustment.staff_note'),
            refundLineItemInputs: $refundLineItems,
            gateway: $transaction->gateway,
            parentTransactionId: Str::shopifyGid($transaction->sourceId, 'OrderTransaction'),
        );

        $lineItems->each(function (LineItem $lineItem) {
            $this->lineItemService->adjustQuantity($lineItem, removeQuantity: 1);
        });
    }

    public function startTrialByTrialGroupId(string $trialGroupId): void
    {
        $order = $this->getByTrialGroupId($trialGroupId);
        if ($order->status === OrderStatus::COMPLETED) {
            return;
        }

        $program = $this->lineItemService->getProgram();
        $trialExpiryDatetime = CarbonImmutable::now()->addDays($program->tryPeriodDays)->addDays($program->dropOffDays);

        try {
            $this->shopifyOrderService->updateShopifyPaymentScheduleDueDate($order->paymentTermsId, $trialExpiryDatetime);
        } catch (ShopifyUpdatePaymentScheduleDueDateOnPaidOrderException $e) {
            // If the order is already paid on Shopify, we can't update the payment schedule due date.
            // This means we should skip the trial, settle payment, and complete the order
            PaymentRequiredEvent::dispatch($order->id, $order->sourceId, $trialGroupId, $order->outstandingCustomerAmount);

            return;
        }

        $order->status = OrderStatus::IN_TRIAL;
        $order->trialExpiresAt = $trialExpiryDatetime;
        $this->update($order);

        foreach ($order->lineItems as $lineItem) {
            if (!$lineItem->isTbyb) {
                continue;
            }

            $lineItem->status = LineItemStatus::IN_TRIAL;
            $this->lineItemService->save($lineItem);
        }

        // TODO: make this a store setting to be exposed on Shoipfy Blackcart Admin
        if (Feature::enabled('shopify-perm-b-merchant-trial-start-email')) {
            // Merchant is sending this email
            return;
        }

        $returnResponse = Http::get('http://localhost:8080/api/stores/settings');
        $returnUrl = Arr::get($returnResponse, 'settings.returnsPortalUrl.value', null);

        Mail::to($order->customerEmail())->send(new OrderDelivered($order, $program?->tryPeriodDays ?? 7, $returnUrl ?? ''));
    }

    public function getByPaymentTermsId(string $paymentTermsId): OrderValue
    {
        return $this->orderRepository->getByPaymentTermsId($paymentTermsId);
    }

    public function expireTrialFromShopifyPaymentSchedule(WebhookPaymentSchedulesDue $paymentSchedulesDue): void
    {
        try {
            $order = $this->getByPaymentTermsId($paymentSchedulesDue->paymentTermsId);
        } catch (ModelNotFoundException $e) {
            // There could be payment schedules that are not Blackcart orders, it's fine to ignore.
            return;
        }

        if ($order->status !== OrderStatus::IN_TRIAL) {
            return;
        }

        try {
            $trialGroup = $this->trialGroupRepository->getByOrder($order);
            $trialGroupId = $trialGroup->id;
        } catch (ModelNotFoundException $e) {
            // The trialGroupId is not used downstream when capturing payment.
            // Substituting with the order ID will have no harm and takes away a point of error.
            $trialGroupId = $order->id;
        }

        PaymentRequiredEvent::dispatch($order->id, $order->sourceId, $trialGroupId, $order->outstandingCustomerAmount);
    }

    public function createOrderFromWebhook($orderData): void
    {
        $orderAlreadyExists = true;
        try {
            $this->getBySourceId($orderData['admin_graphql_api_id']);
        } catch (ModelNotFoundException $e) {
            $orderAlreadyExists = false;
        }

        if ($orderAlreadyExists) {
            return;
        }

        if (!$this->isBlackcartOrderWebhook($orderData)) {
            return;
        }

        $newOrderValue = $this->parseWebhookCreateData($orderData, App::context()->store->id);
        $order = $this->create($newOrderValue);

        if ($newOrderValue->lineItems?->count() > 0) {
            $this->lineItemService->syncCollectionToOrder($newOrderValue->lineItems, $order);
        }

        // TODO: make this a store setting to be exposed on Shoipfy Blackcart Admin
        if (Feature::enabled('shopify-perm-b-merchant-order-confirm-email')) {
            // Merchant is sending this email
            return;
        }

        // Line items are not attached to the order until after the line items are created
        // We need to refresh the order to get the line items
        $refreshedOrder = $this->getById($order->id);

        $returnResponse = Http::get('http://localhost:8080/api/stores/settings');
        $returnUrl = Arr::get($returnResponse, 'settings.returnsPortalUrl.value', null);
        Mail::to($newOrderValue->customerEmail())->send(new OrderConfirmation($refreshedOrder, $returnUrl));
    }

    /**
     * A Blackcart order is one where at least one of the order line items has a selling plan ID that matches to the store's selling plan ID.
     */
    public function isBlackcartOrderWebhook(Collection $orderData): bool
    {
        $program = $this->lineItemService->getProgram();
        if (!$program) {
            return false;
        }
        $merchantShopifySellingPlanId = $program->shopifySellingPlanId;

        $lineItemsWithSellingPlanData = $this->lineItemService->fetchSellingPlans($orderData['admin_graphql_api_id']);

        foreach ($orderData['line_items'] as $lineItem) {
            $hasSellingPlan = !empty($lineItemsWithSellingPlanData[$lineItem['admin_graphql_api_id']]['sellingPlan']);
            if (!$hasSellingPlan) {
                continue;
            }

            $lineItemSellingPlanId = $lineItemsWithSellingPlanData[$lineItem['admin_graphql_api_id']]['sellingPlan']['sellingPlanId'];
            if ($lineItemSellingPlanId === $merchantShopifySellingPlanId) {
                return true;
            }
        }

        return false;
    }

    public function updateOrderStatusAfterLineItemSaved(string $orderId): void
    {
        $order = $this->getById($orderId);

        if ($order->status !== OrderStatus::OPEN) {
            return;
        }

        foreach ($order->lineItems as $lineItem) {
            if ($lineItem->status === LineItemStatus::FULFILLED || $lineItem->status === LineItemStatus::DELIVERED) {
                $order->status = OrderStatus::FULFILLMENT_PARTIALLY_FULFILLED;

                $this->update($order);
                return;
            }
        }
    }
}
