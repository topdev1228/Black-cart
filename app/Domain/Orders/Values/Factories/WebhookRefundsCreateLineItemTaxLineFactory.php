<?php

declare(strict_types=1);

namespace App\Domain\Orders\Values\Factories;

use App\Domain\Shared\Values\Factory;
use App\Domain\Shared\Values\ShopifyMoneySetWithCurrencyCode;

class WebhookRefundsCreateLineItemTaxLineFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->word(),
            'rate' => $this->faker->randomFloat(3, 0.1, 100),
            'priceSet' => ShopifyMoneySetWithCurrencyCode::builder()->create(),
        ];
    }
}
