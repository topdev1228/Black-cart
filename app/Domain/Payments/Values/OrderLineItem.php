<?php
declare(strict_types=1);

namespace App\Domain\Payments\Values;

use App\Domain\Payments\Enums\OrderLineItemStatus;
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
class OrderLineItem extends Value
{
    use HasValueFactory;
    use HasValueCollection;

    public function __construct(
        public ?string $id = null,
        public ?string $orderId = null,
        // #[WithTransformer(StringTransform::class, 'shopifyId')]
        #[WithCast(StringTransform::class, 'shopifyGid:LineItem')]
        public ?string $sourceId = null,
        public int $quantity = 1,
        public OrderLineItemStatus $status = OrderLineItemStatus::OPEN,
        public array $lineItemData = [],
        public ?string $trialableId = null,
        public ?string $trialGroupId = null,
        public bool $isTbyb = false,
        public ?string $sellingPlanId = null,
        public CurrencyAlpha3 $shopCurrency = CurrencyAlpha3::US_Dollar,
        public CurrencyAlpha3 $customerCurrency = CurrencyAlpha3::US_Dollar,
        #[WithCast(MoneyValue::class, 'shopCurrency', isMinorValue: false)]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $priceShopAmount = 0,
        #[WithCast(MoneyValue::class, 'customerCurrency', isMinorValue: false)]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $priceCustomerAmount = 0,
        #[WithCast(MoneyValue::class, 'shopCurrency', isMinorValue: false)]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $totalPriceShopAmount = 0,
        #[WithCast(MoneyValue::class, 'customerCurrency', isMinorValue: false)]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $totalPriceCustomerAmount = 0,
        #[WithCast(MoneyValue::class, 'shopCurrency', isMinorValue: false)]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $discountShopAmount = 0,
        #[WithCast(MoneyValue::class, 'customerCurrency', isMinorValue: false)]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $discountCustomerAmount = 0,
        public float $taxRate = 0,
        #[WithCast(MoneyValue::class, 'shopCurrency', isMinorValue: false)]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $taxShopAmount = 0,
        #[WithCast(MoneyValue::class, 'customerCurrency', isMinorValue: false)]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $taxCustomerAmount = 0,
    ) {
    }

    public function title(): string
    {
        if (!empty($this->lineItemData['name'])) {
            return $this->lineItemData['name'];
        }

        if (!empty($this->id)) {
            return 'LineItem#' . $this->id;
        }

        return 'LineItem';
    }
}
