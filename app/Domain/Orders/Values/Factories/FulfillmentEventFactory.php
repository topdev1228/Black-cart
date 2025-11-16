<?php
declare(strict_types=1);

namespace App\Domain\Orders\Values\Factories;

use App\Domain\Orders\Enums\FulfillmentEventStatus;
use App\Domain\Shared\Values\Factory;

class FulfillmentEventFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => $this->faker->randomNumber(8),
            'fulfillmentId' => $this->faker->randomNumber(8),
            'status' => FulfillmentEventStatus::IN_TRANSIT,
            'message' => 'This item is in transit',
        ];
    }
}
