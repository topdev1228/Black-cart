<?php
declare(strict_types=1);

namespace App\Domain\Orders\Services;

use App;
use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Orders\Enums\OrderSummaryStatus;
use App\Domain\Orders\Repositories\OrderRepository;
use App\Domain\Orders\Values\AnalyticsData;
use App\Domain\Orders\Values\AnalyticsDataRecord;
use Brick\Money\Money;
use Illuminate\Support\Facades\Date;

class AnalyticsService
{
    //TODO: do actual calculation for metrics
    const TBYB_ESTIMATED_FIXED_FEE = 0.04;
    const RETURN_SHIPPING_COST = 8;
    const FULFILLMENT_COST = 8;
    const PAID_ADVERTISING_COST = 0;
    const PAYMENT_PROCESSING_COST = 0.023;
    const PRODUCT_COST = 0.25;

    public function __construct(protected OrderRepository $orderRepository)
    {
    }

    public function get(): AnalyticsData
    {
        $store = App::context()->store;
        $data = [];
        $start = $store->createdAt->startOfMonth()->startOfDay();
        $end = Date::now()->lastOfMonth()->endOfDay();
        foreach ($this->orderRepository->getOrdersByDateRange($start, $end) as $record) {
            $status = $this->orderSummaryStatus($record->status);
            //ignore orders we were not able to relate to order status' we show on the dashboard.
            if (!$status) {
                continue;
            }
            $returnShippingCost = (count($record->returns) > 0 || count($record->refunds) > 0) ? Money::of(static::RETURN_SHIPPING_COST, $store->currency->value) : Money::of(0, $store->currency->value);
            $fulfillmentCost = Money::of(static::FULFILLMENT_COST, $store->currency->value);
            $paymentProcessingCost = $record->originalTotalGrossSalesShopAmount->multipliedBy(static::PAYMENT_PROCESSING_COST, config('money.rounding'));
            $paidAdvertisingCost = Money::of(static::PAID_ADVERTISING_COST, $store->currency->value);
            $productCost = $record->totalNetSalesShopAmount->multipliedBy(static::PRODUCT_COST, config('money.rounding'));
            $tbybFee = $record->totalNetSalesShopAmount->multipliedBy(static::TBYB_ESTIMATED_FIXED_FEE, config('money.rounding'));
            $dataRecord = [
                'orderStatus' => $status,
                'date' => $record->createdAt,
                'orderCount' => 1,
                'grossSales' => $record->originalTotalGrossSalesShopAmount,
                'netSales' => $record->totalNetSalesShopAmount,
                'discounts' => $record->originalTotalDiscountsShopAmount,
                'productCost' => $productCost,
                'fulfillmentCost' => $fulfillmentCost,
                'returnShippingCost' => $returnShippingCost,
                'paymentProcessingCost' => $paymentProcessingCost,
                'paidAdvertisingCost' => $paidAdvertisingCost,
                'tbybFee' => $tbybFee,
                'returns' => $record->tbybRefundGrossSalesShopAmount->plus($record->upfrontRefundGrossSalesShopAmount)->minus($record->tbybRefundDiscountsShopAmount)->minus($record->upfrontRefundDiscountsShopAmount),
                'profitContribution' => $record->totalNetSalesShopAmount->minus($fulfillmentCost)->minus($productCost)->minus($returnShippingCost)->minus($paymentProcessingCost)->minus($tbybFee)->minus($paidAdvertisingCost),
            ];

            $data[] = AnalyticsDataRecord::from($dataRecord);
        }

        return
            AnalyticsData::from([
                'data' => $data,
            ]);
    }

    private function orderSummaryStatus(OrderStatus $status): OrderSummaryStatus|null
    {
        switch ($status) {
            case OrderStatus::OPEN:
            case OrderStatus::PAYMENT_PARTIALLY_PAID:
            case OrderStatus::PAYMENT_AUTHORIZED:
            case OrderStatus::PAYMENT_UNPAID:
                return OrderSummaryStatus::PROCESSING;
            case OrderStatus::FULFILLMENT_FULFILLED:
            case OrderStatus::FULFILLMENT_PARTIALLY_FULFILLED:
                return OrderSummaryStatus::SHIPPED;
            case OrderStatus::IN_TRIAL:
                return OrderSummaryStatus::TRIAL_IN_PROGRESS;
            case OrderStatus::PAYMENT_PAID:
            case OrderStatus::COMPLETED:
                return OrderSummaryStatus::COMPLETED;
            default:
                return null;
        }
    }
}
