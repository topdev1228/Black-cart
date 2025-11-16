<?php
declare(strict_types=1);

namespace App\Domain\Orders\Values;

use App\Domain\Orders\Enums\ReturnStatus;
use App\Domain\Shared\Traits\HasValueCollection;
use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Casts\MoneyValue;
use App\Domain\Shared\Values\Value;
use Brick\Money\Money;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class OrderReturn extends Value
{
    use HasValueCollection;
    use HasValueFactory;

    public function __construct(
        public string $storeId,
        public string $orderId,
        public string $sourceId,
        public string $sourceOrderId,
        public string $name,
        public CurrencyAlpha3 $shopCurrency,
        public CurrencyAlpha3 $customerCurrency,
        public ReturnStatus $status,
        public int $totalQuantity,
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
        #[WithCast(MoneyValue::class, 'shop_currency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money $tbybTaxShopAmount,
        #[WithCast(MoneyValue::class, 'shop_currency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money $upfrontTaxShopAmount,
        #[WithCast(MoneyValue::class, 'customer_currency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money $tbybTaxCustomerAmount,
        #[WithCast(MoneyValue::class, 'customer_currency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money $upfrontTaxCustomerAmount,
        #[WithCast(MoneyValue::class, 'shop_currency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money $tbybTotalShopAmount,
        #[WithCast(MoneyValue::class, 'customer_currency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money $tbybTotalCustomerAmount,
        public array $returnData,
        public ?string $id = null,
    ) {
    }
}
