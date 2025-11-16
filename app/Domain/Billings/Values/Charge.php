<?php

declare(strict_types=1);

namespace App\Domain\Billings\Values;

use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Casts\MoneyValue;
use App\Domain\Shared\Values\Value;
use Brick\Money\Money;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class Charge extends Value
{
    use HasValueFactory;

    protected array $hidden = ['total'];

    public function __construct(
        public CurrencyAlpha3 $currency,
        #[WithCast(MoneyValue::class)]
        public Money $amount,
        #[WithCast(MoneyValue::class)]
        public Money $balance,
        public bool $isBilled,
        #[WithCast(MoneyValue::class)]
        public Money $stepSize,
        #[WithCast(MoneyValue::class)]
        public Money $stepStartAmount,
        #[WithCast(MoneyValue::class)]
        public Money $stepEndAmount,
        public ?string $id = null,
        public ?int $quantity = null,
        public ?string $description = null,
        public ?string $storeId = null,
        public ?string $tbybNetSaleId = null,
        #[WithCast(MoneyValue::class)]
        public ?Money $total = null,
        public ?bool $isFirstOfBillingPeriod = false,
        #[WithCast(DateTimeInterfaceCast::class)]
        public CarbonImmutable|Carbon|null $createdAt = null,
        #[WithCast(DateTimeInterfaceCast::class)]
        public CarbonImmutable|Carbon|null $updatedAt = null,
        #[WithCast(DateTimeInterfaceCast::class)]
        public CarbonImmutable|Carbon|null $timeRangeStart = null,
        #[WithCast(DateTimeInterfaceCast::class)]
        public CarbonImmutable|Carbon|null $timeRangeEnd = null,
        #[WithCast(DateTimeInterfaceCast::class)]
        public CarbonImmutable|Carbon|null $billedAt = null,
    ) {
        if ($this->total === null && $this->quantity !== null) {
            $this->total = $this->amount->multipliedBy($this->quantity);
        }
    }
}
