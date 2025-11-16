<?php
declare(strict_types=1);

namespace App\Domain\Payments\Values\Factories;

use App\Domain\Payments\Enums\OrderLineItemStatus;
use App\Domain\Shared\Values\Factory;

class OrderLineItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'orderId' => $this->faker->uuid(),
            'sourceId' => $this->faker->uuid(),
            'quantity' => 1,
            'status' => OrderLineItemStatus::OPEN,
        ];
    }
}
