<?php
declare(strict_types=1);

namespace App\Domain\Payments\Values\Factories;

use App\Domain\Payments\Enums\OrderStatus;
use App\Domain\Shared\Values\Factory;
use Brick\Money\Money;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        $currency = CurrencyAlpha3::US_Dollar;

        return [
            'id' => $this->faker->uuid(),
            'storeId' => $this->faker->uuid(),
            'sourceId' => $this->faker->uuid(),
            'status' => OrderStatus::OPEN,
            'orderData' => [],
            'blackcartMetadata' => [],
            'shopCurrency' => $currency,
            'customerCurrency' => $currency,
            'originalTotalGrossSalesShopAmount' => Money::of($this->faker->numberBetween(0, 10000), $currency->value),
            'totalNetSalesShopAmount' => Money::of($this->faker->numberBetween(0, 10000), $currency->value),
            'originalTotalDiscountsShopAmount' => Money::of($this->faker->numberBetween(0, 10000), $currency->value),
            'totalOrderRefundsShopAmount' => Money::of($this->faker->numberBetween(0, 10000), $currency->value),
            'tbybRefundDiscountsShopAmount' => Money::of($this->faker->numberBetween(0, 10000), $currency->value),
            'upfrontRefundDiscountsShopAmount' => Money::of($this->faker->numberBetween(0, 10000), $currency->value),
            'outstandingCustomerAmount' => Money::of($this->faker->numberBetween(0, 10000), $currency->value),
            'outstandingShopAmount' => Money::of($this->faker->numberBetween(0, 10000), $currency->value),
        ];
    }
}
