<?php
declare(strict_types=1);

namespace App\Domain\Billings\Database\Factories;

use App\Domain\Billings\Enums\SubscriptionStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Date;

class SubscriptionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'store_id' => $this->faker->uuid(),
            'shopify_app_subscription_id' => null,
            'shopify_confirmation_url' => null,
            'status' => SubscriptionStatus::PENDING,
            'current_period_end' => null,
            'trial_days' => 30,
            'trial_period_end' => null,
            'is_test' => false,
            'activated_at' => null,
            'deactivated_at' => null,
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
        $activatedAt = Date::create(2024, 2, 1, 9, 0, 0);

        return $this->state([
            'status' => SubscriptionStatus::ACTIVE,
            'activated_at' => $activatedAt,
            'deactivated_at' => null,
            'current_period_end' => $activatedAt->copy()->addDays(60),
            'trial_period_end' => $activatedAt->copy()->addDays(30),
        ]);
    }

    public function cancelled(): static
    {
        $activatedAt = Date::create(2024, 2, 1, 9, 0, 0);

        return $this->state([
            'status' => SubscriptionStatus::CANCELLED,
            'activated_at' => $activatedAt,
            'deactivated_at' => Date::create(2024, 2, 19, 17, 0, 0),
            'current_period_end' => $activatedAt->copy()->addDays(60),
            'trial_period_end' => $activatedAt->copy()->addDays(30),
        ]);
    }

    public function declined(): static
    {
        return $this->state([
            'status' => SubscriptionStatus::DECLINED,
            'activated_at' => null,
            'deactivated_at' => Date::create(2024, 2, 19, 17, 0, 0),
            'current_period_end' => null,
            'trial_period_end' => null,
        ]);
    }

    public function expired(): static
    {
        return $this->state([
            'status' => SubscriptionStatus::EXPIRED,
            'activated_at' => null,
            'deactivated_at' => Date::create(2024, 2, 19, 17, 0, 0),
            'current_period_end' => null,
            'trial_period_end' => null,
        ]);
    }

    public function frozen(): static
    {
        $activatedAt = Date::create(2024, 2, 1, 9, 0, 0);

        return $this->state([
            'status' => SubscriptionStatus::FROZEN,
            'activated_at' => $activatedAt,
            'deactivated_at' => Date::create(2024, 2, 19, 17, 0, 0),
            'current_period_end' => $activatedAt->copy()->addDays(60),
            'trial_period_end' => $activatedAt->copy()->addDays(30),
        ]);
    }

    public function pending(): static
    {
        return $this->state([
            'status' => SubscriptionStatus::PENDING,
            'activated_at' => null,
            'deactivated_at' => null,
            'current_period_end' => null,
            'trial_period_end' => null,
        ]);
    }

    public function isTest(): static
    {
        return $this->state([
            'is_test' => true,
        ]);
    }

    public function noTrial(): static
    {
        return $this->state([
            'trial_days' => 0,
            'trial_period_end' => null,
        ]);
    }
}
