<?php
declare(strict_types=1);

namespace App\Domain\Billings\Database\Factories;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChargeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'store_id' => $this->faker->uuid(),
            'tbyb_net_sale_id' => $this->faker->uuid(),
            'currency' => 'USD',
            'amount' => 10000,
            'balance' => 10000,
            'is_billed' => false,
            'billed_at' => null,
            'time_range_start' => CarbonImmutable::now()->subDays(2),
            'time_range_end' => CarbonImmutable::now()->subDay(),
            'step_size' => 25000,
            'step_start_amount' => $this->faker->numberBetween(0, 10) * 25000,
            'step_end_amount' => $this->faker->numberBetween(11, 45) * 25000,
            'is_first_of_billing_period' => false,
        ];
    }
}
