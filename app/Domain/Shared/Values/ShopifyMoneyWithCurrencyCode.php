<?php

declare(strict_types=1);

namespace App\Domain\Shared\Values;

use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Casts\MoneyValue;
use Brick\Money\Money;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class ShopifyMoneyWithCurrencyCode extends Value
{
    use HasValueFactory;

    public function __construct(
        #[WithCast(MoneyValue::class, 'currency_code', isMinorValue: false)]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money $amount,
        public CurrencyAlpha3 $currencyCode,
    ) {
    }
}
