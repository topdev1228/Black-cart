<?php
declare(strict_types=1);

namespace App\Domain\Billings\Values;

use App\Domain\Shared\Traits\HasValueCollection;
use App\Domain\Shared\Values\Casts\MoneyValue;
use App\Domain\Shared\Values\Value;
use Brick\Money\Money;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\WithTransformer;

class UsageConfigEntry extends Value
{
    use HasValueCollection;

    public function __construct(
        public CurrencyAlpha3 $currency,
        #[WithCast(MoneyValue::class)]
        #[WithTransformer(MoneyValue::class)]
        public Money $step,
        #[WithCast(MoneyValue::class)]
        #[WithTransformer(MoneyValue::class)]
        public Money $price,
        #[WithCast(MoneyValue::class)]
        #[WithTransformer(MoneyValue::class)]
        public Money $start,
        #[WithCast(MoneyValue::class)]
        #[WithTransformer(MoneyValue::class)]
        public ?Money $end = null,
    ) {
    }
}
