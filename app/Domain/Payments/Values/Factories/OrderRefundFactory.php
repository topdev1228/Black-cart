<?php

declare(strict_types=1);

namespace App\Domain\Payments\Values\Factories;

use App\Domain\Shared\Values\Factory;

class OrderRefundFactory extends Factory
{
    public function definition(): array
    {
        return [
            'source_refund_reference_id' => $this->faker->uuid(),
            'store_id' => $this->faker->uuid(),
            'order_id' => $this->faker->uuid(),
            'shop_currency' => 'USD',
            'customer_currency' => 'USD',
            'order_level_refund_customer_amount' => $this->faker->numberBetween(100, 100000),
            'order_level_refund_shop_amount' => $this->faker->numberBetween(100, 100000),
            'tbyb_discounts_customer_amount' => $this->faker->numberBetween(100, 100000),
            'tbyb_discounts_shop_amount' => $this->faker->numberBetween(100, 100000),
            'tbyb_gross_sales_customer_amount' => $this->faker->numberBetween(100, 100000),
            'tbyb_gross_sales_shop_amount' => $this->faker->numberBetween(100, 100000),
            'upfront_discounts_customer_amount' => $this->faker->numberBetween(100, 100000),
            'upfront_discounts_shop_amount' => $this->faker->numberBetween(100, 100000),
            'upfront_gross_sales_customer_amount' => $this->faker->numberBetween(100, 100000),
            'upfront_gross_sales_shop_amount' => $this->faker->numberBetween(100, 100000),
        ];
    }
}
