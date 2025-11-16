<?php

declare(strict_types=1);

namespace App\Domain\Orders\Values;

use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Orders\Values\Collections\LineItemCollection;
use App\Domain\Orders\Values\Collections\OrderReturnCollection;
use App\Domain\Orders\Values\Collections\RefundCollection;
use App\Domain\Orders\Values\Collections\TransactionCollection;
use App\Domain\Shared\Traits\HasValueCollection;
use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Casts\MoneyValue;
use App\Domain\Shared\Values\Value;
use Brick\Money\Money;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class Order extends Value
{
    use HasValueFactory;
    use HasValueCollection;

    public function __construct(
        public string $storeId,
        public ?string $id = null,
        public ?string $sourceId = null,
        public OrderStatus $status = OrderStatus::OPEN,
        public array $orderData = [],
        public array $blackcartMetadata = [],
        public ?string $name = null,
        public bool $taxesIncluded = false,
        public bool $taxesExempt = false,
        public ?string $tags = '',
        public ?string $discountCodes = '',
        public bool $test = false,
        public ?string $paymentTermsId = null,
        public ?string $paymentTermsName = null,
        public ?string $paymentTermsType = null,
        public CurrencyAlpha3 $shopCurrency = CurrencyAlpha3::US_Dollar,
        public CurrencyAlpha3 $customerCurrency = CurrencyAlpha3::US_Dollar,
        #[WithCast(MoneyValue::class, 'shopCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $totalShopAmount = 0,
        #[WithCast(MoneyValue::class, 'customerCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $totalCustomerAmount = 0,
        #[WithCast(MoneyValue::class, 'shopCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $outstandingShopAmount = 0,
        #[WithCast(MoneyValue::class, 'customerCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $outstandingCustomerAmount = 0,
        #[WithCast(MoneyValue::class, 'shopCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $originalOutstandingShopAmount = 0,
        #[WithCast(MoneyValue::class, 'customerCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $originalOutstandingCustomerAmount = 0,
        #[WithCast(MoneyValue::class, 'shopCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $originalTbybGrossSalesShopAmount = 0,
        #[WithCast(MoneyValue::class, 'customerCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $originalTbybGrossSalesCustomerAmount = 0,
        #[WithCast(MoneyValue::class, 'shopCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $originalUpfrontGrossSalesShopAmount = 0,
        #[WithCast(MoneyValue::class, 'customerCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $originalUpfrontGrossSalesCustomerAmount = 0,
        #[WithCast(MoneyValue::class, 'shopCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $originalTotalGrossSalesShopAmount = 0,
        #[WithCast(MoneyValue::class, 'customerCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $originalTotalGrossSalesCustomerAmount = 0,
        #[WithCast(MoneyValue::class, 'shopCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $originalTbybDiscountsShopAmount = 0,
        #[WithCast(MoneyValue::class, 'customerCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $originalTbybDiscountsCustomerAmount = 0,
        #[WithCast(MoneyValue::class, 'shopCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $originalUpfrontDiscountsShopAmount = 0,
        #[WithCast(MoneyValue::class, 'customerCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $originalUpfrontDiscountsCustomerAmount = 0,
        #[WithCast(MoneyValue::class, 'shopCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $originalTotalDiscountsShopAmount = 0,
        #[WithCast(MoneyValue::class, 'customerCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $originalTotalDiscountsCustomerAmount = 0,
        #[WithCast(MoneyValue::class, 'shopCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $tbybRefundGrossSalesShopAmount = 0,
        #[WithCast(MoneyValue::class, 'customerCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $tbybRefundGrossSalesCustomerAmount = 0,
        #[WithCast(MoneyValue::class, 'shopCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $upfrontRefundGrossSalesShopAmount = 0,
        #[WithCast(MoneyValue::class, 'customerCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $upfrontRefundGrossSalesCustomerAmount = 0,
        #[WithCast(MoneyValue::class, 'shopCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $totalOrderLevelRefundsShopAmount = 0,
        #[WithCast(MoneyValue::class, 'customerCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $totalOrderLevelRefundsCustomerAmount = 0,
        #[WithCast(MoneyValue::class, 'shopCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $tbybRefundDiscountsShopAmount = 0,
        #[WithCast(MoneyValue::class, 'customerCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $tbybRefundDiscountsCustomerAmount = 0,
        #[WithCast(MoneyValue::class, 'shopCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $upfrontRefundDiscountsShopAmount = 0,
        #[WithCast(MoneyValue::class, 'customerCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $upfrontRefundDiscountsCustomerAmount = 0,
        #[WithCast(MoneyValue::class, 'shopCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $tbybNetSalesShopAmount = 0,
        #[WithCast(MoneyValue::class, 'customerCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $tbybNetSalesCustomerAmount = 0,
        #[WithCast(MoneyValue::class, 'shopCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $upfrontNetSalesShopAmount = 0,
        #[WithCast(MoneyValue::class, 'customerCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $upfrontNetSalesCustomerAmount = 0,
        #[WithCast(MoneyValue::class, 'shopCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $totalNetSalesShopAmount = 0,
        #[WithCast(MoneyValue::class, 'customerCurrency')]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public Money|int $totalNetSalesCustomerAmount = 0,
        #[WithCast(DateTimeInterfaceCast::class)]
        public CarbonImmutable|Carbon|null $completedAt = null,
        #[WithCast(DateTimeInterfaceCast::class)]
        public CarbonImmutable|Carbon|null $assumedDeliveryMerchantEmailSentAt = null,
        #[WithCast(DateTimeInterfaceCast::class)]
        public CarbonImmutable|Carbon|null $createdAt = null,
        #[DataCollectionOf(LineItem::class)]
        public ?LineItemCollection $lineItems = null,
        #[DataCollectionOf(Refund::class)]
        public ?RefundCollection $refunds = null,
        #[DataCollectionOf(OrderReturn::class)]
        public ?OrderReturnCollection $returns = null,
        #[DataCollectionOf(Transaction::class)]
        public ?TransactionCollection $transactions = null,
        #[WithCast(DateTimeInterfaceCast::class)]
        public CarbonImmutable|Carbon|null $trialExpiresAt = null,
    ) {
    }

    public function isBlackcartOrder(): bool
    {
        return !empty($this->orderData['payment_terms']);
    }

    public function customerEmail(): string
    {
        return $this->orderData['email'] ?? '';
    }

    public function customerFirstName(): string
    {
        return $this->orderData['customer']['first_name'] ?? '';
    }

    public function orderName(): string
    {
        return $this->orderData['name'] ?? '';
    }

    public function hydrateLineItems(): self
    {
        $lineItems = [];
        foreach ($this->orderData['line_items'] as $lineItem) {
            $lineItems[$lineItem['admin_graphql_api_id']] = $lineItem;
        }

        foreach ($this->lineItems->items() as $itemValue) {
            if (empty($lineItems[$itemValue->sourceId])) {
                continue;
            }

            $itemValue->lineItemData = $lineItems[$itemValue->sourceId];
        }

        return $this;
    }
}
