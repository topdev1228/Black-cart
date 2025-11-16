<?php
declare(strict_types=1);

namespace Tests\Fixtures\Values;

use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Casts\MoneyValue;
use App\Domain\Shared\Values\Value;
use Brick\Money\Money;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\WithTransformer;

class TestValueWithMoney extends Value
{
    use HasValueFactory;

    public function __construct(
        #[WithCast(MoneyValue::class)]
        #[WithTransformer(MoneyValue::class)]
        public ?Money $amount,
        public CurrencyAlpha3 $currency,
    ) {
    }
}
