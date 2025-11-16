<?php
declare(strict_types=1);

namespace App\Domain\Billings\Database\Factories;

use App\Domain\Billings\Enums\SubscriptionLineItemType;
use App\Domain\Billings\Models\Subscription;
use Brick\Money\Currency;
use Brick\Money\Money;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionLineItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'subscription_id' => Subscription::factory(),
            'shopify_app_subscription_id' => 'gid://shopify/AppSubscription/1234567890',
            'shopify_app_subscription_line_item_id' => 'gid://shopify/AppSubscriptionLineItem/usage1212121212?v=1&index=0',
            'type' => SubscriptionLineItemType::USAGE,
            'terms' => 'First $2,500 in Blackcart net sales included in subscription. Then pay $100 for every additional $2,500 in net sales.',
            'recurring_amount' => Money::of(0, 'USD'),
            'recurring_amount_currency' => Currency::of('USD')->getCurrencyCode(),
            'usage_capped_amount' => Money::of(500, 'USD'),
            'usage_capped_amount_currency' => Currency::of('USD')->getCurrencyCode(),
        ];
    }

    public function recurringSubscription(): static
    {
        return $this->state([
            'shopify_app_subscription_line_item_id' => 'gid://shopify/AppSubscriptionLineItem/recurring1313131313?v=1&index=0',
            'type' => SubscriptionLineItemType::RECURRING,
            'terms' => '$99 every 30 days.  First $2,500 in Blackcart net sales included in subscription.  Free 30 day trial.',
            'recurring_amount' => Money::of(99, 'USD'),
            'recurring_amount_currency' => Currency::of('USD')->getCurrencyCode(),
            'usage_capped_amount' => Money::of(0, 'USD'),
            'usage_capped_amount_currency' => Currency::of('USD')->getCurrencyCode(),
        ]);
    }
}
