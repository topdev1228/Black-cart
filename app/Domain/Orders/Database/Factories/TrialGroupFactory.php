<?php

declare(strict_types=1);

namespace App\Domain\Orders\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TrialGroupFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid(),
            'order_id' => $this->faker->uuid(),
        ];
    }
}
