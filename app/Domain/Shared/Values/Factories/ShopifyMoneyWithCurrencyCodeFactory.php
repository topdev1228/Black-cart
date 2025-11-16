<?php

declare(strict_types=1);

namespace App\Domain\Shared\Values\Factories;

use App\Domain\Shared\Values\Factory;
use Brick\Money\Money;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;

class ShopifyMoneyWithCurrencyCodeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'amount' => Money::ofMinor($this->faker->numberBetween(100, 10000), 'USD'),
            'currencyCode' => CurrencyAlpha3::US_Dollar,
        ];
    }
}
