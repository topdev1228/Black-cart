<?php
declare(strict_types=1);

namespace App\Domain\Orders\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ReturnLineItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_return_id' => $this->faker->uuid(),
            'source_id' => $this->faker->uuid(),
            'source_return_id' => $this->faker->uuid(),
            'line_item_id' => $this->faker->uuid(),
            'quantity' => $this->faker->numberBetween(1, 10),
            'return_reason' => $this->faker->word(),
            'return_reason_note' => $this->faker->sentence(),
            'return_line_item_data' => [],
            'customer_note' => $this->faker->sentence(),
            'is_tbyb' => $this->faker->boolean(),
        ];
    }
}
