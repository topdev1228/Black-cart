<?php
declare(strict_types=1);

namespace App\Domain\Orders\Values;

namespace App\Domain\Orders\Values;

use App\Domain\Shared\Values\Casts\MoneyValue;
use App\Domain\Shared\Values\Value;
use Brick\Money\Money;
use Carbon\CarbonImmutable;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

/**
 * @see ReAuthFailedEvent
 */
#[MapName(SnakeCaseMapper::class)]
class ReAuthFailedEvent extends Value
{
    public function __construct(
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d\TH:i:s.u\Z')]
        public CarbonImmutable $authExpiry,
        #[WithCast(MoneyValue::class, isMinorValue: true)]
        public Money $authAmount,
        public CurrencyAlpha3 $currency,
        public string $sourceOrderId
    ) {
    }
}
