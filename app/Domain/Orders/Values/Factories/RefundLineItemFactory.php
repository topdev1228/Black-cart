<?php

declare(strict_types=1);

namespace App\Domain\Orders\Values\Factories;

use App\Domain\Shared\Values\Factory;
use Brick\Money\Money;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;

class RefundLineItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'refund_id' => $this->faker()->uuid(),
            'source_refund_reference_id' => $this->faker->uuid(),
            'line_item_id' => $this->faker->uuid(),
            'quantity' => $this->faker->randomNumber(),
            'is_tbyb' => true,
            'shop_currency' => CurrencyAlpha3::US_Dollar,
            'customer_currency' => CurrencyAlpha3::US_Dollar,
            'deposit_customer_amount' => Money::ofMinor($this->faker->numberBetween(100, 1000), 'USD'),
            'deposit_shop_amount' => Money::ofMinor($this->faker->numberBetween(100, 1000), 'USD'),
            'discounts_customer_amount' => Money::ofMinor($this->faker->numberBetween(100, 10000), 'USD'),
            'discounts_shop_amount' => Money::ofMinor($this->faker->numberBetween(100, 10000), 'USD'),
            'gross_sales_customer_amount' => Money::ofMinor($this->faker->numberBetween(100, 10000), 'USD'),
            'gross_sales_shop_amount' => Money::ofMinor($this->faker->numberBetween(100, 10000), 'USD'),
            'tax_customer_amount' => Money::ofMinor($this->faker->numberBetween(100, 1000), 'USD'),
            'tax_shop_amount' => Money::ofMinor($this->faker->numberBetween(100, 1000), 'USD'),
            'total_customer_amount' => Money::ofMinor($this->faker->numberBetween(100, 10000), 'USD'),
            'total_shop_amount' => Money::ofMinor($this->faker->numberBetween(100, 10000), 'USD'),
        ];
    }
}
