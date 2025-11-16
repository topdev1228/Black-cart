<?php
declare(strict_types=1);

namespace App\Domain\Stores\Database\Factories;

use App\Domain\Stores\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

class StoreSettingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'store_id' => Store::factory(),
            'name' => $this->faker->unique()->word(),
            'value' => $this->faker->word(),
            'is_secure' => false,
        ];
    }

    public function secure(): static
    {
        return $this->state([
            'is_secure' => true,
        ]);
    }
}
