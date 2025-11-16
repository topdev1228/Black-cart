<?php
declare(strict_types=1);

namespace App\Domain\Billings\Database\Factories;

use Brick\Money\Money;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;

class TbybNetSaleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'store_id' => $this->faker->uuid(),
            'date_start' => CarbonImmutable::now(),
            'date_end' => CarbonImmutable::now(),
            'time_range_start' => CarbonImmutable::now()->startOfDay(),
            'time_range_end' => CarbonImmutable::now()->endOfDay(),
            'currency' => CurrencyAlpha3::US_Dollar,
            'tbyb_gross_sales' => Money::ofMinor(10000, 'USD'),
            'tbyb_discounts' => Money::ofMinor(1000, 'USD'),
            'tbyb_refunded_gross_sales' => Money::ofMinor(500, 'USD'),
            'tbyb_refunded_discounts' => Money::ofMinor(500, 'USD'),
            'tbyb_net_sales' => Money::ofMinor(8500, 'USD'),
            'is_first_of_billing_period' => false,
        ];
    }
}
