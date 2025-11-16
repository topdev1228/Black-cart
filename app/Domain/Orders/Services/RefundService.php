<?php
declare(strict_types=1);

namespace App\Domain\Orders\Services;

use App;
use App\Domain\Orders\Enums\LineItemStatus;
use App\Domain\Orders\Exceptions\InternalRefundException;
use App\Domain\Orders\Repositories\OrderRepository;
use App\Domain\Orders\Repositories\RefundLineItemRepository;
use App\Domain\Orders\Repositories\RefundRepository;
use App\Domain\Orders\Values\Refund;
use App\Domain\Orders\Values\RefundLineItem;
use App\Domain\Orders\Values\WebhookRefundsCreate;
use App\Domain\Orders\Values\WebhookRefundsCreateOrderAdjustment;
use App\Domain\Orders\Values\WebhookRefundsCreateRefundLineItem;
use Brick\Money\Money;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Str;

class RefundService
{
    public function __construct(protected OrderRepository $orderRepository, protected RefundRepository $refundsRepository, protected RefundLineItemRepository $refundLineItemRepository, protected LineItemService $lineItemService)
    {
    }

    public function createFromWebhook(WebhookRefundsCreate $refundData): ?Refund
    {
        $id = $refundData->sourceId;
        $orderId = $refundData->sourceOrderId;

        try {
            $order = $this->orderRepository->getBySourceId($orderId);
        } catch (ModelNotFoundException) {
            return null;
        }

        $refund = Refund::from(Refund::empty(), [
            'id' => Str::uuid(),
            'source_refund_reference_id' => $id,
            'order_id' => $order->id,
            'store_id' => App::context()->store->id,
            'shop_currency' => 'USD',
            'customer_currency' => 'USD',
            'order_level_refund_customer_amount' => Money::zero('USD'),
            'order_level_refund_shop_amount' => Money::zero('USD'),
            'refunded_customer_amount' => Money::zero('USD'),
            'refunded_shop_amount' => Money::zero('USD'),
            'tbyb_deposit_customer_amount' => Money::zero('USD'),
            'tbyb_deposit_shop_amount' => Money::zero('USD'),
            'tbyb_discounts_customer_amount' => Money::zero('USD'),
            'tbyb_discounts_shop_amount' => Money::zero('USD'),
            'tbyb_gross_sales_customer_amount' => Money::zero('USD'),
            'tbyb_gross_sales_shop_amount' => Money::zero('USD'),
            'tbyb_total_customer_amount' => Money::zero('USD'),
            'tbyb_total_shop_amount' => Money::zero('USD'),
            'upfront_discounts_customer_amount' => Money::zero('USD'),
            'upfront_discounts_shop_amount' => Money::zero('USD'),
            'upfront_gross_sales_customer_amount' => Money::zero('USD'),
            'upfront_gross_sales_shop_amount' => Money::zero('USD'),
            'upfront_total_customer_amount' => Money::zero('USD'),
            'upfront_total_shop_amount' => Money::zero('USD'),
            'refund_data' => $refundData->toArray(),
        ]);

        $refund->shopCurrency = $order->shopCurrency;
        $refund->customerCurrency = $order->customerCurrency;
        $refund->tbybDepositShopAmount = Money::zero($order->shopCurrency->value);
        $refund->tbybDepositCustomerAmount = Money::zero($order->customerCurrency->value);
        $refund->tbybDiscountsCustomerAmount = Money::zero($order->customerCurrency->value);
        $refund->tbybDiscountsShopAmount = Money::zero($order->shopCurrency->value);
        $refund->tbybGrossSalesCustomerAmount = Money::zero($order->customerCurrency->value);
        $refund->tbybGrossSalesShopAmount = Money::zero($order->shopCurrency->value);
        $refund->tbybTotalCustomerAmount = Money::zero($order->customerCurrency->value);
        $refund->tbybTotalShopAmount = Money::zero($order->shopCurrency->value);
        $refund->upfrontDiscountsCustomerAmount = Money::zero($order->customerCurrency->value);
        $refund->upfrontDiscountsShopAmount = Money::zero($order->shopCurrency->value);
        $refund->upfrontGrossSalesCustomerAmount = Money::zero($order->customerCurrency->value);
        $refund->upfrontGrossSalesShopAmount = Money::zero($order->shopCurrency->value);
        $refund->upfrontTotalCustomerAmount = Money::zero($order->customerCurrency->value);
        $refund->upfrontTotalShopAmount = Money::zero($order->shopCurrency->value);
        $refund->refundedShopAmount = Money::zero($order->shopCurrency->value);
        $refund->refundedCustomerAmount = Money::zero($order->customerCurrency->value);

        $refundHasLineItems = (bool) $refundData->refundLineItems?->count();

        if ($refundHasLineItems) {
            // Add Line Item refunds, if any
            try {
                $refundData->refundLineItems?->each(function (WebhookRefundsCreateRefundLineItem $refundLineItem) use ($refundData, &$refund, $order) {
                    $lineItem = $this->lineItemService->getBySourceId(Str::shopifyGid($refundLineItem->lineItemId, 'LineItem'));

                    if ($lineItem->status === LineItemStatus::INTERNAL) {
                        // Skip refunds for internal line items (order adjustments)
                        throw new InternalRefundException();
                    }

                    $refundLineItemData = [
                        'refund_id' => $refund->id,
                        'source_refund_reference_id' => $refundData->sourceId,
                        'line_item_id' => $lineItem->id,
                        'quantity' => $refundLineItem->quantity,
                        'shop_currency' => $lineItem->shopCurrency,
                        'customer_currency' => $lineItem->customerCurrency,
                        'gross_sales_shop_amount' => $refundLineItem->lineItem->priceSet->shopMoney->amount->multipliedBy($refundLineItem->quantity, config('money.rounding')),
                        'gross_sales_customer_amount' => $refundLineItem->lineItem->priceSet->presentmentMoney->amount->multipliedBy($refundLineItem->quantity, config('money.rounding')),
                        'tax_shop_amount' => $refundLineItem->totalTaxSet?->shopMoney->amount ?? Money::zero($order->shopCurrency->value),
                        'tax_customer_amount' => $refundLineItem->totalTaxSet?->presentmentMoney->amount ?? Money::zero($order->customerCurrency->value),
                        'is_tbyb' => $lineItem->isTbyb,
                        'deposit_shop_amount' => Money::zero($order->shopCurrency->value),
                        'deposit_customer_amount' => Money::zero($order->customerCurrency->value),
                    ];

                    $refundLineItemData['discounts_shop_amount'] = $refundLineItemData['gross_sales_shop_amount']->minus($refundLineItem->subtotalSet->shopMoney->amount, config('money.rounding'));
                    $refundLineItemData['discounts_customer_amount'] = $refundLineItemData['gross_sales_customer_amount']->minus($refundLineItem->subtotalSet->presentmentMoney->amount, config('money.rounding'));
                    if ($lineItem->depositShopAmount->isPositive()) { // Deposit could be zero for TBYB items, and is zero for upfront items
                        $refundLineItemData['deposit_shop_amount'] = $lineItem->depositShopAmount->dividedBy($lineItem->quantity, config('money.rounding'))->multipliedBy($refundLineItem->quantity, config('money.rounding'));
                        $refundLineItemData['deposit_customer_amount'] = $lineItem->depositCustomerAmount->dividedBy($lineItem->quantity, config('money.rounding'))->multipliedBy($refundLineItem->quantity, config('money.rounding'));
                    }

                    $refundLineItemData['total_shop_amount'] = $refundLineItem->subtotalSet->shopMoney->amount
                        ->plus($refundLineItem->totalTaxSet?->shopMoney->amount ?? 0, config('money.rounding'));
                    $refundLineItemData['total_customer_amount'] = $refundLineItem->subtotalSet->presentmentMoney->amount
                        ->plus($refundLineItem->totalTaxSet?->presentmentMoney->amount ?? 0, config('money.rounding'));

                    $refundedLineItem = RefundLineItem::from($refundLineItemData);
                    $this->refundLineItemRepository->create($refundedLineItem);

                    // Add to the refund totals
                    if ($lineItem->isTbyb) {
                        $refund->tbybGrossSalesShopAmount = $refund->tbybGrossSalesShopAmount->plus($refundedLineItem->grossSalesShopAmount, config('money.rounding'));
                        $refund->tbybGrossSalesCustomerAmount = $refund->tbybGrossSalesCustomerAmount->plus($refundedLineItem->grossSalesCustomerAmount, config('money.rounding'));

                        $refund->tbybDepositShopAmount = $refund->tbybDepositShopAmount->plus($refundedLineItem->depositShopAmount, config('money.rounding'));
                        $refund->tbybDepositCustomerAmount = $refund->tbybDepositCustomerAmount->plus($refundedLineItem->depositCustomerAmount, config('money.rounding'));

                        $refund->tbybDiscountsShopAmount = $refund->tbybDiscountsShopAmount->plus($refundedLineItem->discountsShopAmount, config('money.rounding'));
                        $refund->tbybDiscountsCustomerAmount = $refund->tbybDiscountsCustomerAmount->plus($refundedLineItem->discountsCustomerAmount, config('money.rounding'));

                        $refund->tbybTotalShopAmount = $refund->tbybTotalShopAmount->plus($refundedLineItem->totalShopAmount, config('money.rounding'));
                        $refund->tbybTotalCustomerAmount = $refund->tbybTotalCustomerAmount->plus($refundedLineItem->totalCustomerAmount, config('money.rounding'));
                    }

                    if (!$lineItem->isTbyb) {
                        $refund->upfrontGrossSalesShopAmount = $refund->upfrontGrossSalesShopAmount->plus($refundedLineItem->grossSalesShopAmount, config('money.rounding'));
                        $refund->upfrontGrossSalesCustomerAmount = $refund->upfrontGrossSalesCustomerAmount->plus($refundedLineItem->grossSalesCustomerAmount, config('money.rounding'));

                        $refund->upfrontDiscountsShopAmount = $refund->upfrontDiscountsShopAmount->plus($refundedLineItem->discountsShopAmount, config('money.rounding'));
                        $refund->upfrontDiscountsCustomerAmount = $refund->upfrontDiscountsCustomerAmount->plus($refundedLineItem->discountsCustomerAmount, config('money.rounding'));

                        $refund->upfrontTotalShopAmount = $refund->upfrontTotalShopAmount->plus($refundedLineItem->totalShopAmount, config('money.rounding'));
                        $refund->upfrontTotalCustomerAmount = $refund->upfrontTotalCustomerAmount->plus($refundedLineItem->totalCustomerAmount, config('money.rounding'));
                    }

                    $this->lineItemService->adjustQuantity($lineItem, removeQuantity: $refundLineItem->quantity);
                });
            } catch (InternalRefundException) {
                return null;
            }
        }

        $refund->orderLevelRefundShopAmount = Money::zero($order->shopCurrency->value);
        $refund->orderLevelRefundCustomerAmount = Money::zero($order->customerCurrency->value);

        // Add order-level adjustments, if there are adjustments and there are no refundLineItems
        if ($refundData->orderLevelRefundAdjustments?->count() && !$refundHasLineItems) {
            $refundData->orderLevelRefundAdjustments->each(function (WebhookRefundsCreateOrderAdjustment $orderLevelRefundAdjustment) use (&$refund) {
                if ($orderLevelRefundAdjustment->amountSet->shopMoney->amount->isNegativeOrZero()) {
                    return;
                }

                $refund->orderLevelRefundShopAmount = $refund->orderLevelRefundShopAmount->plus($orderLevelRefundAdjustment->amountSet->shopMoney->amount, config('money.rounding'));
                $refund->orderLevelRefundCustomerAmount = $refund->orderLevelRefundCustomerAmount->plus($orderLevelRefundAdjustment->amountSet->presentmentMoney->amount, config('money.rounding'));
            });

            $refund->orderLevelRefundShopAmount = $refund->orderLevelRefundShopAmount
                ->minus($refund->tbybTotalShopAmount, config('money.rounding'))
                ->minus($refund->tbybDepositShopAmount, config('money.rounding'))
                ->minus($refund->upfrontTotalShopAmount, config('money.rounding'));
            $refund->orderLevelRefundCustomerAmount = $refund->orderLevelRefundCustomerAmount
                ->minus($refund->tbybTotalCustomerAmount, config('money.rounding'))
                ->minus($refund->tbybDepositCustomerAmount, config('money.rounding'))
                ->minus($refund->upfrontTotalCustomerAmount, config('money.rounding'));
        }

        /*
         * Add total refunded amount
         * If there are no refund line items, these should equal the orderLevelRefund*Amounts above
         */
        $lastAdjustment = $refundData->orderLevelRefundAdjustments->last();
        if (!is_null($lastAdjustment) && $refundData->transactions?->count()) {
            $refund->refundedShopAmount = $refund->refundedShopAmount->plus($lastAdjustment->amountSet->shopMoney->amount);
            $refund->refundedCustomerAmount = $refund->refundedCustomerAmount->plus($lastAdjustment->amountSet->presentmentMoney->amount);
        }

        return $this->refundsRepository->create($refund);
    }

    public function getGrossSales(CarbonImmutable $endDate, ?CarbonImmutable $startDate = null): string
    {
        return $this->refundsRepository->getGrossSales($endDate, $startDate);
    }

    public function getDiscounts(CarbonImmutable $endDate, ?CarbonImmutable $startDate = null): string
    {
        return $this->refundsRepository->getDiscounts($endDate, $startDate);
    }
}
