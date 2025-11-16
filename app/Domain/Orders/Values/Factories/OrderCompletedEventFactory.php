<?php

declare(strict_types=1);

namespace App\Domain\Orders\Values\Factories;

use App\Domain\Orders\Values\Order;
use App\Domain\Shared\Values\Factory;

class OrderCompletedEventFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order' => Order::builder()->create(),
        ];
    }
}
