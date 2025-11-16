<?php

declare(strict_types=1);

namespace App\Domain\Shared\Values\Factories;

use App\Domain\Shared\Values\Factory;
use App\Domain\Shared\Values\ShopifyMoneyWithCurrencyCode;

class ShopifyMoneySetWithCurrencyCodeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'shopMoney' => ShopifyMoneyWithCurrencyCode::builder()->create(),
            'presentmentMoney' => ShopifyMoneyWithCurrencyCode::builder()->create(),
        ];
    }
}
