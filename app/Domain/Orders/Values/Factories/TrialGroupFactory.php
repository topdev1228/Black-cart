<?php

declare(strict_types=1);

namespace App\Domain\Orders\Values\Factories;

use App\Domain\Shared\Values\Factory;

class TrialGroupFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid(),
            'orderId' => $this->faker->uuid(),
        ];
    }
}
