<?php
declare(strict_types=1);

namespace App\Domain\Orders\Values;

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
class ReturnLineItem extends Value
{
    use HasValueCollection;
    use HasValueFactory;

    public function __construct(
        public string $orderReturnId,
        public string $sourceId,
        public string $sourceReturnId,
        public string $lineItemId,
        public int $quantity,
        public string $returnReason,
        public string $returnReasonNote,
        public array $returnLineItemData,
        public CurrencyAlpha3 $shopCurrency,
        public CurrencyAlpha3 $customerCurrency,
        #[WithCast(MoneyValue::class, 'shop_currency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money $grossSalesShopAmount,
        #[WithCast(MoneyValue::class, 'shop_currency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money $discountsShopAmount,
        #[WithCast(MoneyValue::class, 'customer_currency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money $grossSalesCustomerAmount,
        #[WithCast(MoneyValue::class, 'customer_currency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money $discountsCustomerAmount,
        #[WithCast(MoneyValue::class, 'shop_currency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money $taxShopAmount,
        #[WithCast(MoneyValue::class, 'customer_currency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money $taxCustomerAmount,
        public bool $isTbyb,
        public ?string $customerNote = '',
        public ?string $id = null,
    ) {
    }
}
