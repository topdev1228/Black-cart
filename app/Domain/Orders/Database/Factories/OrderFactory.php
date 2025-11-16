<?php
declare(strict_types=1);

namespace App\Domain\Orders\Database\Factories;

use App\Domain\Orders\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;

class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $currency = CurrencyAlpha3::US_Dollar;

        return [
            'id' => $this->faker->uuid(),
            'store_id' => $this->faker->uuid(),
            'source_id' => 'gid://shopify/Order/' . $this->faker->randomNumber(9),
            'name' => '#' . $this->faker->randomNumber(5),
            'status' => OrderStatus::OPEN,
            'order_data' => [],
            'blackcart_metadata' => [],
            'shop_currency' => $currency,
            'customer_currency' => $currency,
            'assumed_delivery_merchant_email_sent_at' => null,
            'trial_expires_at' => null,
            'payment_terms_id' => strval($this->faker->randomNumber(9)),

            // Defaults to tbyb only cart, no discounts, no refunds
            'total_shop_amount' => 10000,
            'total_customer_amount' => 10000,
            'original_outstanding_shop_amount' => 10000,
            'original_outstanding_customer_amount' => 10000,
            'outstanding_shop_amount' => 10000,
            'outstanding_customer_amount' => 10000,
            'original_tbyb_gross_sales_shop_amount' => 10000,
            'original_tbyb_gross_sales_customer_amount' => 10000,
            'original_tbyb_discounts_shop_amount' => 0,
            'original_tbyb_discounts_customer_amount' => 0,
            'original_upfront_gross_sales_shop_amount' => 0,
            'original_upfront_gross_sales_customer_amount' => 0,
            'original_upfront_discounts_shop_amount' => 0,
            'original_upfront_discounts_customer_amount' => 0,
            'original_total_discounts_shop_amount' => 0,
            'original_total_discounts_customer_amount' => 0,
            'original_total_gross_sales_shop_amount' => 10000,
            'original_total_gross_sales_customer_amount' => 10000,
            'tbyb_refund_gross_sales_shop_amount' => 0,
            'tbyb_refund_gross_sales_customer_amount' => 0,
            'upfront_refund_gross_sales_shop_amount' => 0,
            'upfront_refund_gross_sales_customer_amount' => 0,
            'tbyb_refund_discounts_shop_amount' => 0,
            'tbyb_refund_discounts_customer_amount' => 0,
            'upfront_refund_discounts_shop_amount' => 0,
            'upfront_refund_discounts_customer_amount' => 0,
            'total_order_level_refunds_shop_amount' => 0,
            'total_order_level_refunds_customer_amount' => 0,
            'tbyb_net_sales_shop_amount' => 10000,
            'tbyb_net_sales_customer_amount' => 10000,
            'upfront_net_sales_shop_amount' => 0,
            'upfront_net_sales_customer_amount' => 0,
            'total_net_sales_shop_amount' => 10000,
            'total_net_sales_customer_amount' => 10000,
        ];
    }

    public function tbybOnly(int $grossShopAmount = 10000, int $grossCustomerAmount = 10000): static
    {
        return $this->state([
            'original_tbyb_gross_sales_shop_amount' => $grossShopAmount,
            'original_tbyb_gross_sales_customer_amount' => $grossCustomerAmount,
            'original_upfront_gross_sales_shop_amount' => 0,
            'original_upfront_gross_sales_customer_amount' => 0,
        ]);
    }

    public function withTbybDiscount(int $discountShopAmount = 2000, $discountCustomerAmount = 2000): static
    {
        return $this->state([
            'original_tbyb_discounts_shop_amount' => $discountShopAmount,
            'original_tbyb_discounts_customer_amount' => $discountCustomerAmount,
        ]);
    }

    public function mixedCart(
        int $tbybGrossShopAmount = 10000,
        int $tbybGrossCustomerAmount = 10000,
        int $upfrontGrossShopAmount = 31000,
        int $upfrontGrossCustomerAmount = 31000
    ): static {
        return $this->state([
            'original_tbyb_gross_sales_shop_amount' => $tbybGrossShopAmount,
            'original_tbyb_gross_sales_customer_amount' => $tbybGrossCustomerAmount,
            'original_upfront_gross_sales_shop_amount' => $upfrontGrossShopAmount,
            'original_upfront_gross_sales_customer_amount' => $upfrontGrossCustomerAmount,
        ]);
    }

    public function withUpfrontDiscount(int $discountShopAmount = 4500, $discountCustomerAmount = 4500): static
    {
        return $this->state([
            'original_upfront_discounts_shop_amount' => $discountShopAmount,
            'original_upfront_discounts_customer_amount' => $discountCustomerAmount,
        ]);
    }

    public function netSales(
        int $tbybNetShopAmount = 10000,
        int $tbybNetCustomerAmount = 10000,
        int $upfrontNetShopAmount = 0,
        int $upfrontNetCustomerAmount = 0
    ): static {
        return $this->state([
            'tbyb_net_sales_shop_amount' => $tbybNetShopAmount,
            'tbyb_net_sales_customer_amount' => $tbybNetCustomerAmount,
            'upfront_net_sales_shop_amount' => $upfrontNetShopAmount,
            'upfront_net_sales_customer_amount' => $upfrontNetCustomerAmount,
            'total_net_sales_shop_amount' => $tbybNetShopAmount + $upfrontNetShopAmount,
            'total_net_sales_customer_amount' => $tbybNetCustomerAmount + $upfrontNetCustomerAmount,
        ]);
    }
}
