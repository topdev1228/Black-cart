<?php
declare(strict_types=1);

namespace App\Domain\Orders\Repositories;

use App\Domain\Orders\Models\Order;
use App\Domain\Orders\Values\Collections\OrderCollection;
use App\Domain\Orders\Values\Order as OrderValue;
use Carbon\CarbonImmutable;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;
use Str;

class OrderRepository
{
    public function __construct(protected LineItemRepository $lineItemRepository)
    {
    }

    /**
     * @param string $id the Order id
     * @param bool $restrictForStore if true, only return Orders if they belong to app()->context->store
     */
    public function getById(string $id, bool $restrictForStore = true): OrderValue
    {
        $order = $restrictForStore
            ? Order::findOrFail($id)
            : Order::withoutCurrentStore()->without(['lineItems', 'refunds', 'returns', 'transactions'])->findOrFail($id);

        return OrderValue::from($order);
    }

    public function getByTrialGroupId(string $trialGroupId): OrderValue
    {
        $order = Order::findOrFail($this->lineItemRepository->getByTrialGroupId($trialGroupId)->orderId);

        return OrderValue::from($order);
    }

    public function getBySourceId(string $sourceId): OrderValue
    {
        return OrderValue::from(Order::where(['source_id' => Str::shopifyGid($sourceId, 'Order')])->firstOrFail());
    }

    public function getByPaymentTermsId(string $paymentTermsId): OrderValue
    {
        return OrderValue::from(Order::where('payment_terms_id', $paymentTermsId)->firstOrFail());
    }

    public function update(OrderValue $value): OrderValue
    {
        $order = Order::findOrFail($value->id);
        $order->update($value->toArray());

        return OrderValue::from($order);
    }

    public function create(OrderValue $value): OrderValue
    {
        return OrderValue::from(Order::create($value->toArray()));
    }

    /**
     * @psalm-suppress InvalidReturnType
     * @psalm-suppress InvalidReturnStatement
     */
    public function all(): OrderCollection
    {
        /** @psalm-suppress InvalidArgument */
        return OrderValue::collection(Order::all());
    }

    public function getStoreIdsByDate(CarbonImmutable $cutoff): array
    {
        return Order::withoutCurrentStore()
            ->where('created_at', '>=', $cutoff)->groupBy('store_id')->pluck('store_id')->toArray();
    }

    public function getGrossSales(CarbonImmutable $endDate, ?CarbonImmutable $startDate): string
    {
        $query = Order::where('created_at', '<', $endDate);

        if ($startDate !== null) {
            $query->where('created_at', '>=', $startDate);
        }

        $grossSales = $query->sum('original_tbyb_gross_sales_shop_amount');

        return (string) $grossSales ?? '0';
    }

    public function getTotalDiscounts(CarbonImmutable $endDate, ?CarbonImmutable $startDate): string
    {
        $query = Order::where('created_at', '<', $endDate);

        if ($startDate !== null) {
            $query->where('created_at', '>=', $startDate);
        }

        $totalDiscounts = $query->sum('original_total_discounts_shop_amount');

        return (string) $totalDiscounts ?? '0';
    }

    public function getShopCurrency(): CurrencyAlpha3
    {
        return Order::firstOrFail()->shop_currency;
    }

    public function getOrdersByDateRange(CarbonImmutable $startDate, CarbonImmutable $endDate): OrderCollection
    {
        /** @psalm-suppress InvalidArgument */
        return OrderValue::collection(Order::where('created_at', '>=', $startDate->startOfMonth()->startOfDay())
            ->where('created_at', '<=', $endDate->lastOfMonth()->endOfDay())->get()->all());
    }
}
