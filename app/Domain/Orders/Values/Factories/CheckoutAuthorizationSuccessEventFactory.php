<?php

declare(strict_types=1);

namespace App\Domain\Orders\Values\Factories;

use App\Domain\Shared\Values\Factory;

class CheckoutAuthorizationSuccessEventFactory extends Factory
{
    public function definition(): array
    {
        return [
            'orderId' => $this->faker->uuid(),
            'sourceOrderId' => $this->faker->uuid(),
        ];
    }
}
