<?php

declare(strict_types=1);

namespace App\Domain\Orders\Values;

use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Casts\MoneyValue;
use App\Domain\Shared\Values\Casts\StringTransform;
use App\Domain\Shared\Values\Value;
use Brick\Money\Money;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class RefundLineItem extends Value
{
    use HasValueFactory;

    public function __construct(
        public string $refundId,
        #[WithCast(StringTransform::class, 'shopifyGid:Refund')]
        public string $sourceRefundReferenceId,
        #[WithCast(StringTransform::class, 'shopifyGid:LineItem')]
        public string $lineItemId,
        public int $quantity,
        public bool $isTbyb,
        public CurrencyAlpha3 $shopCurrency,
        public CurrencyAlpha3 $customerCurrency,
        #[WithCast(MoneyValue::class, 'customer_currency', isMinorValue: true)]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money $depositCustomerAmount,
        #[WithCast(MoneyValue::class, 'shop_currency', isMinorValue: true)]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money $depositShopAmount,
        #[WithCast(MoneyValue::class, 'customer_currency', isMinorValue: true)]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money $discountsCustomerAmount,
        #[WithCast(MoneyValue::class, 'shop_currency', isMinorValue: true)]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money $discountsShopAmount,
        #[WithCast(MoneyValue::class, 'customer_currency', isMinorValue: true)]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money $grossSalesCustomerAmount,
        #[WithCast(MoneyValue::class, 'shop_currency', isMinorValue: true)]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money $grossSalesShopAmount,
        #[WithCast(MoneyValue::class, 'customer_currency', isMinorValue: true)]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money $taxCustomerAmount,
        #[WithCast(MoneyValue::class, 'shop_currency', isMinorValue: true)]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money $taxShopAmount,
        #[WithCast(MoneyValue::class, 'customer_currency', isMinorValue: true)]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money $totalCustomerAmount,
        #[WithCast(MoneyValue::class, 'shop_currency', isMinorValue: true)]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money $totalShopAmount,
    ) {
    }
}
