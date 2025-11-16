<?php
declare(strict_types=1);

namespace App\Domain\Orders\Values\Factories;

use App\Domain\Orders\Enums\OrderStatus;
use App\Domain\Shared\Values\Factory;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        $currency = CurrencyAlpha3::US_Dollar;

        return [
            'store_id' => $this->faker->uuid(),
            'source_id' => 'gid://shopify/Order/' . $this->faker->randomNumber(9),
            'name' => '#' . $this->faker->randomNumber(5),
            'status' => OrderStatus::OPEN,
            'order_data' => [],
            'blackcart_metadata' => [],
            'shop_currency' => CurrencyAlpha3::US_Dollar,
            'customer_currency' => CurrencyAlpha3::US_Dollar,
            'total_shop_amount' => $this->faker->numberBetween(0, 10000),
            'total_customer_amount' => $this->faker->numberBetween(0, 10000),
            'original_outstanding_shop_amount' => $this->faker->numberBetween(0, 10000),
            'original_outstanding_customer_amount' => $this->faker->numberBetween(0, 10000),
            'outstanding_shop_amount' => $this->faker->numberBetween(0, 10000),
            'outstanding_customer_amount' => $this->faker->numberBetween(0, 10000),
            'original_total_gross_sales_shop_amount' => $this->faker->numberBetween(0, 10000),
            'total_net_sales_shop_amount' => $this->faker->numberBetween(0, 10000),
            'original_total_discounts_shop_amount' => $this->faker->numberBetween(0, 10000),
            'total_order_level_refunds_shop_amount' => $this->faker->numberBetween(0, 10000),
            'tbyb_refund_discounts_shop_amount' => $this->faker->numberBetween(0, 10000),
            'upfront_refund_discounts_shop_amount' => $this->faker->numberBetween(0, 10000),
            'assumed_delivery_merchant_email_sent_at' => null,
            'trial_expires_at' => null,
            'payment_terms_id' => strval($this->faker->randomNumber(9)),
        ];
    }
}
