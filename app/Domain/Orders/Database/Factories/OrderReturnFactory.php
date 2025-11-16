<?php
declare(strict_types=1);

namespace App\Domain\Orders\Database\Factories;

use App\Domain\Orders\Enums\ReturnStatus;
use App\Domain\Orders\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderReturnFactory extends Factory
{
    public function definition(): array
    {
        return [
            'store_id' => $this->faker->uuid(),
            'order_id' => Order::factory(),
            'source_id' => 'gid://shopify/Return/' . $this->faker->randomNumber(9),
            'source_order_id' => 'gid://shopify/Order/' . $this->faker->randomNumber(9),
            'name' => '#' . $this->faker->randomNumber(5),
            'status' => ReturnStatus::OPEN,
            'total_quantity' => $this->faker->numberBetween(1, 10),
            'tbyb_gross_sales_shop_amount' => $this->faker->numberBetween(100, 100000),
            'upfront_gross_sales_shop_amount' => $this->faker->numberBetween(100, 100000),
            'tbyb_gross_sales_customer_amount' => $this->faker->numberBetween(100, 100000),
            'upfront_gross_sales_customer_amount' => $this->faker->numberBetween(100, 100000),
            'tbyb_discounts_shop_amount' => $this->faker->numberBetween(100, 100000),
            'tbyb_discounts_customer_amount' => $this->faker->numberBetween(100, 100000),
            'upfront_discounts_shop_amount' => $this->faker->numberBetween(100, 100000),
            'upfront_discounts_customer_amount' => $this->faker->numberBetween(100, 100000),
            'tbyb_tax_shop_amount' => $this->faker->numberBetween(100, 100000),
            'tbyb_tax_customer_amount' => $this->faker->numberBetween(100, 100000),
            'upfront_tax_shop_amount' => $this->faker->numberBetween(100, 100000),
            'upfront_tax_customer_amount' => $this->faker->numberBetween(100, 100000),
            'tbyb_total_shop_amount' => $this->faker->numberBetween(100, 100000),
            'tbyb_total_customer_amount' => $this->faker->numberBetween(100, 100000),
            'customer_currency' => 'USD',
            'shop_currency' => 'USD',
            'return_data' => [],
        ];
    }
}
