<?php

declare(strict_types=1);

namespace App\Domain\Orders\Values\Factories;

use App\Domain\Shared\Values\Factory;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;

class WebhookRefundsCreateTransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'orderId' => 'gid://shopify/Order/' . $this->faker->randomNumber(8),
            'currency' => CurrencyAlpha3::US_Dollar,
        ];
    }
}
