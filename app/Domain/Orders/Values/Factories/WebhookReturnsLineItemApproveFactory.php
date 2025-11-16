<?php

declare(strict_types=1);

namespace App\Domain\Orders\Values\Factories;

use App\Domain\Orders\Values\WebhookReturnsLineItemFulfillmentApprove;
use App\Domain\Shared\Values\Factory;

class WebhookReturnsLineItemApproveFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' =>$this->faker->numberBetween(0, 100),
            'admin_graphql_api_id' => $this->faker->uuid(),
            'fulfillment_line_item' => WebhookReturnsLineItemFulfillmentApprove::builder()->create(),
            'quantity' => $this->faker->numberBetween(1, 10),
            'return_reason' => $this->faker->word(),
            'return_reason_note' => $this->faker->sentence(),
            'customer_note' => $this->faker->sentence(),
        ];
    }
}
