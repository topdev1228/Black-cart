<?php

declare(strict_types=1);

namespace App\Domain\Billings\Values\Factories;

use App\Domain\Billings\Enums\SubscriptionLineItemType;
use App\Domain\Shared\Values\Factory;
use Brick\Money\Currency;
use Brick\Money\Money;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;

class SubscriptionLineItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'shopify_app_subscription_id' => 'gid://shopify/AppSubscription/1234567890',
            'shopify_app_subscription_line_item_id' => 'gid://shopify/AppSubscriptionLineItem/usage1212121212?v=1&index=0',
            'type' => SubscriptionLineItemType::USAGE,
            'terms' => 'The first $2,500 in Blackcart Try Before You Buy net sales included in subscription. Then pay $100 for every additional $2,500 in Try Before You Buy net sales. Unlimited usage during the 30 day free trial.',
            'recurring_amount' => Money::of(0, 'USD'),
            'recurring_amount_currency' => CurrencyAlpha3::US_Dollar,
            'usage_capped_amount' => Money::of(2500, 'USD'),
            'usage_capped_amount_currency' => CurrencyAlpha3::US_Dollar,
        ];
    }

    public function recurringSubscription(): static
    {
        return $this->state([
            'shopify_app_subscription_line_item_id' => 'gid://shopify/AppSubscriptionLineItem/recurring1313131313?v=1&index=0',
            'type' => SubscriptionLineItemType::RECURRING,
            'terms' => '$99 every 30 days.  First $2,500 in Blackcart Try Before You Buy net sales included in subscription.  Free 30 day trial with unlimited usage.',
            'recurring_amount' => Money::of(99, 'USD'),
            'recurring_amount_currency' => Currency::of('USD'),
            'usage_capped_amount' => Money::of(0, 'USD'),
            'usage_capped_amount_currency' => Currency::of('USD'),
        ]);
    }
}
