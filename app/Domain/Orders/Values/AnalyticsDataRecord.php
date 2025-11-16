<?php
declare(strict_types=1);

namespace App\Domain\Orders\Values;

use App\Domain\Orders\Enums\OrderSummaryStatus;
use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Value;
use Brick\Money\Money;
use Carbon\CarbonImmutable;

class AnalyticsDataRecord extends Value
{
    use HasValueFactory;

    public function __construct(
        public OrderSummaryStatus $orderStatus,
        public CarbonImmutable $date,
        public int $orderCount,
        public Money $grossSales,
        public Money $netSales,
        public ?Money $profitContribution,
        public ?Money $discounts,
        public ?Money $productCost,
        public ?Money $fulfillmentCost,
        public ?Money $returnShippingCost,
        public ?Money $paymentProcessingCost,
        public ?Money $paidAdvertisingCost,
        public ?Money $tbybFee,
        public ?Money $returns
    ) {
    }
}
