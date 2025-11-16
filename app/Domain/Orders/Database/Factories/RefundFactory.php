<?php
declare(strict_types=1);

namespace App\Domain\Orders\Database\Factories;

use App\Domain\Orders\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class RefundFactory extends Factory
{
    public function definition(): array
    {
        return [
            'source_refund_reference_id' => 'gid://shopify/Refund/' . $this->faker->randomNumber(9),
            'order_id' => Order::factory(),
            'store_id' => $this->faker->uuid(),
            'shop_currency' => 'USD',
            'customer_currency' => 'USD',

            // Defaults to refunding a single TBYB item with no discount and no order level refund
            'order_level_refund_customer_amount' => 0,
            'order_level_refund_shop_amount' => 0,
            'refunded_customer_amount' => 1000,
            'refunded_shop_amount' => 1000,
            'tbyb_deposit_customer_amount' => 0,
            'tbyb_deposit_shop_amount' => 0,
            'tbyb_discounts_customer_amount' => 0,
            'tbyb_discounts_shop_amount' => 0,
            'tbyb_gross_sales_customer_amount' => 10000,
            'tbyb_gross_sales_shop_amount' => 10000,
            'tbyb_total_customer_amount' => 10000,
            'tbyb_total_shop_amount' => 10000,
            'upfront_discounts_customer_amount' => 0,
            'upfront_discounts_shop_amount' => 0,
            'upfront_gross_sales_customer_amount' => 0,
            'upfront_gross_sales_shop_amount' => 0,
            'upfront_total_customer_amount' => 0,
            'upfront_total_shop_amount' => 0,
        ];
    }

    public function tbyb(int $grossShopAmount = 10000, int $grossCustomerAmount = 10000): static
    {
        return $this->state([
            'tbyb_gross_sales_shop_amount' => $grossShopAmount,
            'tbyb_gross_sales_customer_amount' => $grossCustomerAmount,
        ]);
    }

    public function withTbybDiscount(int $discountShopAmount = 2000, $discountCustomerAmount = 2000): static
    {
        return $this->state([
            'tbyb_discounts_shop_amount' => $discountShopAmount,
            'tbyb_discounts_customer_amount' => $discountCustomerAmount,
        ]);
    }

    public function upfront(int $grossShopAmount = 31000, int $grossCustomerAmount = 31000): static
    {
        return $this->state([
            'upfront_gross_sales_shop_amount' => $grossShopAmount,
            'upfront_gross_sales_customer_amount' => $grossCustomerAmount,
        ]);
    }

    public function withUpfrontDiscount(int $discountShopAmount = 4500, $discountCustomerAmount = 4500): static
    {
        return $this->state([
            'upfront_discounts_shop_amount' => $discountShopAmount,
            'upfront_discounts_customer_amount' => $discountCustomerAmount,
        ]);
    }

    public function withOrderLevelRefund($shopAmount = 2900, $customerAmount = 2900): static
    {
        return $this->state([
            'order_level_refund_shop_amount' => $shopAmount,
            'order_level_refund_customer_amount' => $customerAmount,
        ]);
    }
}
