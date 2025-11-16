<?php

declare(strict_types=1);

namespace App\Domain\Orders\Values\Factories;

use App\Domain\Shared\Values\Factory;
use App\Domain\Shared\Values\ShopifyMoneySetWithCurrencyCode;

class WebhookRefundsCreateLineItemDiscountAllocationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'discountApplicationIndex' => $this->faker->randomNumber(),
            'amountSet' => ShopifyMoneySetWithCurrencyCode::builder()->create(),
        ];
    }
}
