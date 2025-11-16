<?php

declare(strict_types=1);

namespace App\Domain\Orders\Values\Factories;

use App\Domain\Shared\Values\Factory;
use App\Domain\Shared\Values\ShopifyMoneySetWithCurrencyCode;

class WebhookRefundsCreateOrderAdjustmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'orderId' => 'gid://shopify/Order/' . $this->faker->randomNumber(8),
            'amountSet' => ShopifyMoneySetWithCurrencyCode::builder()->create(),
            'taxAmountSet' => ShopifyMoneySetWithCurrencyCode::builder()->create(),
        ];
    }
}
