<?php

declare(strict_types=1);

namespace App\Domain\Orders\Values\Factories;

use App\Domain\Shared\Values\Factory;

class TrialGroupExpiredEventFactory extends Factory
{
    public function definition(): array
    {
        return [
            'group_key' => $this->faker->uuid(),
        ];
    }
}
