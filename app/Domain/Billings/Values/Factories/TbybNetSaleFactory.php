<?php
declare(strict_types=1);

namespace App\Domain\Billings\Values\Factories;

use App\Domain\Shared\Values\Factory;
use Brick\Money\Money;
use Illuminate\Support\Facades\Date;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;

class TbybNetSaleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'store_id' => $this->faker->uuid(),
            'date_start' => Date::now(),
            'date_end' => Date::now(),
            'time_range_start' => Date::now()->startOfDay(),
            'time_range_end' => Date::now()->endOfDay(),
            'currency' => CurrencyAlpha3::US_Dollar,
            'tbyb_gross_sales' => Money::ofMinor(10000, 'USD'),
            'tbyb_discounts' => Money::ofMinor(1000, 'USD'),
            'tbyb_refunded_gross_sales' => Money::ofMinor(500, 'USD'),
            'tbyb_refunded_discounts' => Money::ofMinor(500, 'USD'),
            'tbyb_net_sales' => Money::ofMinor(8500, 'USD'),
        ];
    }
}
