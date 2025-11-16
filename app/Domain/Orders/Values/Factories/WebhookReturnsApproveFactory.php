<?php

declare(strict_types=1);

namespace App\Domain\Orders\Values\Factories;

use App\Domain\Orders\Values\WebhookReturnsApproveOrder;
use App\Domain\Orders\Values\WebhookReturnsLineItemApprove;
use App\Domain\Shared\Values\Factory;

class WebhookReturnsApproveFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid(),
            'admin_graphql_api_id' => $this->faker->uuid(),
            'status' => $this->faker->word(),
            'name' => $this->faker->word(),
            'order' => WebhookReturnsApproveOrder::builder()->create(),
            'total_return_line_items' => $this->faker->numberBetween(1, 10),
            'return_line_items' => WebhookReturnsLineItemApprove::collection([WebhookReturnsLineItemApprove::builder()->create()]),
        ];
    }
}
