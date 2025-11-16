<?php

declare(strict_types=1);

namespace App\Domain\Payments\Values;

use App\Domain\Shared\Traits\HasValueCollection;
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
class OrderRefund extends Value
{
    use HasValueFactory;
    use HasValueCollection;

    public function __construct(
        #[WithCast(StringTransform::class, 'shopifyGid:Refund')]
        public string $sourceRefundReferenceId,
        public string $orderId,
        public CurrencyAlpha3 $shopCurrency,
        public CurrencyAlpha3 $customerCurrency,
        public string $storeId,
        #[WithCast(MoneyValue::class, 'shop_currency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money $tbybGrossSalesShopAmount,
        #[WithCast(MoneyValue::class, 'shop_currency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money $upfrontGrossSalesShopAmount,
        #[WithCast(MoneyValue::class, 'shop_currency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money $tbybDiscountsShopAmount,
        #[WithCast(MoneyValue::class, 'shop_currency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money $upfrontDiscountsShopAmount,
        #[WithCast(MoneyValue::class, 'shop_currency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money $orderLevelRefundShopAmount,
        #[WithCast(MoneyValue::class, 'shop_currency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money $tbybTotalShopAmount,
        #[WithCast(MoneyValue::class, 'shop_currency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money $upfrontTotalShopAmount,
        #[WithCast(MoneyValue::class, 'customer_currency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money $tbybGrossSalesCustomerAmount,
        #[WithCast(MoneyValue::class, 'customer_currency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money $upfrontGrossSalesCustomerAmount,
        #[WithCast(MoneyValue::class, 'customer_currency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money $tbybDiscountsCustomerAmount,
        #[WithCast(MoneyValue::class, 'customer_currency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money $upfrontDiscountsCustomerAmount,
        #[WithCast(MoneyValue::class, 'customer_currency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money $orderLevelRefundCustomerAmount,
        #[WithCast(MoneyValue::class, 'customer_currency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money $tbybTotalCustomerAmount,
        #[WithCast(MoneyValue::class, 'customer_currency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money $upfrontTotalCustomerAmount,
        public ?string $id = null,
    ) {
    }
}
