<?php

declare(strict_types=1);

namespace App\Domain\Shopify\Values;

use App\Domain\Billings\Enums\SubscriptionLineItemType;
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
class SubscriptionLineItem extends Value
{
    use HasValueFactory;
    use HasValueCollection;

    public function __construct(
        public ?string $id = null,
        public ?string $subscriptionId = null,
        public ?string $shopifyAppSubscriptionId = null,
        public ?string $shopifyAppSubscriptionLineItemId = null,
        public SubscriptionLineItemType $type = SubscriptionLineItemType::USAGE,
        public string $terms = '$100 for $2500 in net sales',

        // For recurring billing
        #[WithCast(MoneyValue::class, 'recurring_amount_currency', true)]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public ?Money $recurringAmount = null,
        public CurrencyAlpha3 $recurringAmountCurrency = CurrencyAlpha3::US_Dollar,

        // For usage based billing
        #[WithCast(MoneyValue::class, 'usage_capped_amount_currency', true)]
        #[WithTransformer(MoneyValue::class, isMinorValue: true)]
        public ?Money $usageCappedAmount = null,
        public CurrencyAlpha3 $usageCappedAmountCurrency = CurrencyAlpha3::US_Dollar,
    ) {
    }
}
