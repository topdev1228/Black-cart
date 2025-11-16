<?php

declare(strict_types=1);

namespace App\Domain\Billings\Values\Factories;

use App\Domain\Billings\Enums\SubscriptionStatus;
use App\Domain\Shared\Values\Factory;
use Carbon\CarbonImmutable;

class SubscriptionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'shopify_app_subscription_id' => null,
            'shopify_confirmation_url' => null,
            'status' => SubscriptionStatus::PENDING,
            'current_period_start' => null,
            'current_period_end' => null,
            'trial_days' => 30,
            'trial_period_end' => null,
            'is_test' => false,
            'activated_at' => null,
            'deactivated_at' => null,
            'subscription_line_items' => [],
        ];
    }

    public function withShopifyData(): static
    {
        return $this->state([
            'shopify_app_subscription_id' => 'gid://shopify/AppSubscription/1234567890',
            'shopify_confirmation_url' => 'https://xxx.myshopify.com/admin/charges/4028497976/confirm_recurring_application_charge?signature=BAh7BzoHaWRsKwc4AB7wOhJhdXRvX2FjdGl2YXRlVA%3D%3D--987b3537018fdd69c50f13d6cbd3fba468e0e9a6',
        ]);
    }

    public function active(): static
    {
        $activatedAt = CarbonImmutable::create(2024, 5, 1);

        return $this->state([
            'status' => SubscriptionStatus::ACTIVE,
            'activated_at' => $activatedAt,
            'deactivated_at' => null,
            'current_period_start' => $activatedAt,
            'current_period_end' => $activatedAt->addDays(30),
            'trial_period_end' => $activatedAt->addDays(30),
        ]);
    }
}
