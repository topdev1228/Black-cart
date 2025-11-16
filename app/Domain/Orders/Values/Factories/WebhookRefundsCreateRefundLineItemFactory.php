<?php

declare(strict_types=1);

namespace App\Domain\Orders\Values\Factories;

use App\Domain\Orders\Values\WebhookRefundsCreateLineItem;
use App\Domain\Shared\Values\Factory;
use App\Domain\Shared\Values\ShopifyMoneySetWithCurrencyCode;

class WebhookRefundsCreateRefundLineItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid(),
            'lineItemId' => $this->faker->uuid(),
            'quantity' => $this->faker->randomNumber(),
            'restockType' => $this->faker->word(),
            'subtotalSet' => ShopifyMoneySetWithCurrencyCode::builder()->create(),
            'lineItem' => WebhookRefundsCreateLineItem::builder()->create(),
            'totalTaxSet' => ShopifyMoneySetWithCurrencyCode::builder()->create(),
        ];
    }
}
