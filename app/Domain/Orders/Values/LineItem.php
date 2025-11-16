<?php
declare(strict_types=1);

namespace App\Domain\Orders\Values;

use App\Domain\Orders\Enums\DepositType;
use App\Domain\Orders\Enums\LineItemDecisionStatus;
use App\Domain\Orders\Enums\LineItemStatus;
use App\Domain\Orders\Enums\LineItemStatusUpdatedBy;
use App\Domain\Shared\Traits\HasValueCollection;
use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Casts\MoneyValue;
use App\Domain\Shared\Values\Casts\StringTransform;
use App\Domain\Shared\Values\Value;
use Brick\Money\Money;
use Carbon\CarbonImmutable;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class LineItem extends Value
{
    use HasValueFactory;
    use HasValueCollection;

    public function __construct(
        public ?string $id = null,
        public ?string $orderId = null,
        #[WithCast(StringTransform::class, 'shopifyGid:Order')]
        public ?string $sourceOrderId = null,
        // #[WithTransformer(StringTransform::class, 'shopifyId')]
        #[WithCast(StringTransform::class, 'shopifyGid:LineItem')]
        public ?string $sourceId = null,
        #[WithCast(StringTransform::class, 'shopifyGid:Product')]
        public ?string $sourceProductId = null,
        #[WithCast(StringTransform::class, 'shopifyGid:Variant')]
        public ?string $sourceVariantId = null,
        public string $productTitle = '',
        public LineItemStatusUpdatedBy $statusUpdatedBy = LineItemStatusUpdatedBy::SHOPIFY,
        public ?string $variantTitle = null,
        public ?string $thumbnail = null,
        public int $quantity = 1,
        public ?int $originalQuantity = 1,
        public LineItemStatus $status = LineItemStatus::OPEN,
        public LineItemDecisionStatus $decisionStatus = LineItemDecisionStatus::KEPT,
        public array $lineItemData = [],
        public ?string $trialableId = null,
        public ?string $trialGroupId = null,
        public bool $isTbyb = false,
        public ?string $sellingPlanId = null,
        public ?DepositType $depositType = null,
        public ?int $depositValue = null,
        public CurrencyAlpha3 $shopCurrency = CurrencyAlpha3::US_Dollar,
        public CurrencyAlpha3 $customerCurrency = CurrencyAlpha3::US_Dollar,
        #[WithCast(MoneyValue::class, 'shopCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $priceShopAmount = 0,
        #[WithCast(MoneyValue::class, 'customerCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $priceCustomerAmount = 0,
        #[WithCast(MoneyValue::class, 'shopCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $totalPriceShopAmount = 0,
        #[WithCast(MoneyValue::class, 'customerCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $totalPriceCustomerAmount = 0,
        #[WithCast(MoneyValue::class, 'shopCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $discountShopAmount = 0,
        #[WithCast(MoneyValue::class, 'customerCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $discountCustomerAmount = 0,
        public float $taxRate = 0,
        #[WithCast(MoneyValue::class, 'shopCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $taxShopAmount = 0,
        #[WithCast(MoneyValue::class, 'customerCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $taxCustomerAmount = 0,
        #[WithCast(MoneyValue::class, 'shopCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $depositShopAmount = 0,
        #[WithCast(MoneyValue::class, 'customerCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $depositCustomerAmount = 0,
        #[WithCast(DateTimeInterfaceCast::class)]
        public CarbonImmutable|null $createdAt = null,
        #[WithCast(DateTimeInterfaceCast::class)]
        public CarbonImmutable|null $updatedAt = null,
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

    /**
     * Returns a subtitle for the line item.
     *
     * This will usually be the variant string (Large, Red)
     * But could also represent things like "Sample"
     */
    public function subtitle(): string
    {
        /*
         * This is empty because we don't have the data.
         * But Ive begun plugging this around (mostly mail) that will load up once we have the value
         */
        return '';
    }
}
