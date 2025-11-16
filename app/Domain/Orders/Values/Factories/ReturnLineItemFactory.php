<?php
declare(strict_types=1);

namespace App\Domain\Orders\Values\Factories;

use App\Domain\Shared\Values\Factory;

class ReturnLineItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'orderReturnId' => $this->faker->uuid(),
            'sourceId' => $this->faker->uuid(),
            'sourceReturnId' => $this->faker->uuid(),
            'lineItemId' => $this->faker->uuid(),
            'quantity' => $this->faker->numberBetween(1, 10),
            'returnReason' => $this->faker->word(),
            'returnReasonNote' => $this->faker->sentence(),
            'customerNote' => $this->faker->sentence(),
            'shop_currency' => 'USD',
            'customer_currency' => 'USD',
            'gross_sales_shop_amount' => $this->faker->numberBetween(100, 100000),
            'gross_sales_customer_amount' => $this->faker->numberBetween(100, 100000),
            'discounts_shop_amount' => $this->faker->numberBetween(100, 100000),
            'discounts_customer_amount' => $this->faker->numberBetween(100, 100000),
            'tax_shop_amount' => $this->faker->numberBetween(100, 1000),
            'tax_customer_amount' => $this->faker->numberBetween(100, 1000),
            'is_tbyb' => $this->faker->boolean(),
            'returnLineItemData' => [],
        ];
    }
}
