<?php
declare(strict_types=1);

namespace App\Domain\Orders\Repositories;

use App\Domain\Orders\Models\Refund;
use App\Domain\Orders\Values\Refund as RefundValue;
use Carbon\CarbonImmutable;

class RefundRepository
{
    public function create(RefundValue $refund)
    {
        return RefundValue::from(Refund::create($refund->toArray()));
    }

    public function getGrossSales(CarbonImmutable $endDate, ?CarbonImmutable $startDate): string
    {
        $query = Refund::where('created_at', '<', $endDate);

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        return (string) ($query->sum('tbyb_gross_sales_shop_amount') ?? '0');
    }

    public function getDiscounts(CarbonImmutable $endDate, ?CarbonImmutable $startDate): string
    {
        $query = Refund::where('created_at', '<', $endDate);

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        return (string) ($query->sum('tbyb_discounts_shop_amount') ?? '0');
    }
}
