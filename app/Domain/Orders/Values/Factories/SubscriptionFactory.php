<?php

declare(strict_types=1);

namespace App\Domain\Orders\Values\Factories;

use App\Domain\Billings\Enums\SubscriptionStatus;
use App\Domain\Shared\Values\Factory;
use Carbon\CarbonImmutable;

class SubscriptionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid,
            'store_id' => $this->faker->uuid,
            'status' => SubscriptionStatus::PENDING,
            'current_period_start' => null,
            'current_period_end' => null,
            'trial_days' => 30,
            'trial_period_end' => null,
            'is_test' => false,
            'activated_at' => null,
            'deactivated_at' => null,
        ];
    }

    public function active(): static
    {
        $activatedAt = CarbonImmutable::now()->subDays(5);

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
