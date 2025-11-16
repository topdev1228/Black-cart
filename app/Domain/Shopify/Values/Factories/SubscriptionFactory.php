<?php

declare(strict_types=1);

namespace App\Domain\Shopify\Values\Factories;

use App\Domain\Shared\Values\Factory;
use App\Domain\Shopify\Enums\SubscriptionStatus;

class SubscriptionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'shopify_app_subscription_id' => null,
            'shopify_confirmation_url' => null,
            'status' => SubscriptionStatus::PENDING,
            'activated_at' => null,
            'deactivated_at' => null,
            'line_items' => [],
        ];
    }

    public function withShopifyData(): static
    {
        return $this->state([
            'shopify_app_subscription_id' => 'gid://shopify/AppSubscription/1234567890',
            'shopify_confirmation_url' => 'https://xxx.myshopify.com/admin/charges/4028497976/confirm_recurring_application_charge?signature=BAh7BzoHaWRsKwc4AB7wOhJhdXRvX2FjdGl2YXRlVA%3D%3D--987b3537018fdd69c50f13d6cbd3fba468e0e9a6',
        ]);
    }
}
