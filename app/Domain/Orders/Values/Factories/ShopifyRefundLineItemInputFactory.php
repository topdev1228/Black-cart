<?php

declare(strict_types=1);

namespace App\Domain\Orders\Values\Factories;

use App\Domain\Orders\Enums\ShopifyRefundLineItemRestockType;
use App\Domain\Shared\Values\Factory;

class ShopifyRefundLineItemInputFactory extends Factory
{
    public function definition(): array
    {
        return [
            'lineItemId' => $this->faker->uuid(),
            'restockType' => ShopifyRefundLineItemRestockType::NO_RESTOCK,
            'locationId' => $this->faker->uuid(),
            'quantity' => 1,
        ];
    }
}
