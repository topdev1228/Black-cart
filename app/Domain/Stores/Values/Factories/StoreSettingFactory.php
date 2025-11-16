<?php
declare(strict_types=1);

namespace App\Domain\Stores\Values\Factories;

use App\Domain\Shared\Values\Factory;

class StoreSettingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'store_id' => $this->faker->uuid(),
            'name' => $this->faker->unique()->word(),
            'value' => $this->faker->word(),
        ];
    }
}
