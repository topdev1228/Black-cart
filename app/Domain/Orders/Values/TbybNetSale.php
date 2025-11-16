<?php
declare(strict_types=1);

namespace App\Domain\Orders\Values;

use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Casts\MoneyValue;
use App\Domain\Shared\Values\Value;
use Brick\Money\Money;
use Carbon\CarbonImmutable;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class TbybNetSale extends Value
{
    use HasValueFactory;

    public function __construct(
        public string $storeId,
        #[WithCast(DateTimeInterfaceCast::class, 'format:Y-m-d')]
        public CarbonImmutable $dateStart,
        #[WithCast(DateTimeInterfaceCast::class, 'format:Y-m-d')]
        public CarbonImmutable $dateEnd,
        #[WithCast(DateTimeInterfaceCast::class)]
        public CarbonImmutable $timeRangeStart,
        #[WithCast(DateTimeInterfaceCast::class)]
        public CarbonImmutable $timeRangeEnd,
        public CurrencyAlpha3 $currency,
        #[WithCast(MoneyValue::class, 'currency', true)]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money $tbybGrossSales,
        #[WithCast(MoneyValue::class, 'currency', true)]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money $tbybDiscounts,
        #[WithCast(MoneyValue::class, 'currency', true)]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money $tbybRefundedGrossSales,
        #[WithCast(MoneyValue::class, 'currency', true)]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money $tbybRefundedDiscounts,
        #[WithCast(MoneyValue::class, 'currency', true)]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money $tbybNetSales,
        public bool $isFirstOfBillingPeriod = false,
        public ?string $id = null
    ) {
    }
}
