<?php

declare(strict_types=1);

namespace App\Domain\Orders\Values\Factories;

use App\Domain\Shared\Values\Factory;

class WebhookReturnsApproveOrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => $this->faker->numberBetween(0, 100),
            'admin_graphql_api_id' => $this->faker->uuid(),
        ];
    }
}
