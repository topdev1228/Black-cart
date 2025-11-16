<?php

declare(strict_types=1);

namespace App\Domain\Billings\Values\Factories;

use App\Domain\Shared\Values\Factory;
use Brick\Money\Money;
use Carbon\CarbonImmutable;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;

class ChargeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid(),
            'storeId' => $this->faker->uuid(),
            'tbybNetSaleId' => $this->faker->uuid(),
            'currency' => CurrencyAlpha3::US_Dollar,
            'amount' => Money::ofMinor($this->faker->numberBetween(100, 10000), 'USD'),
            'balance' => Money::ofMinor($this->faker->numberBetween(100, 10000), 'USD'),
            'isBilled' => $this->faker->boolean(),
            'billedAt' => null,
            'timeRangeStart' => CarbonImmutable::now()->subDays(2),
            'timeRangeEnd' => CarbonImmutable::now()->subDay(),
            'stepSize' => Money::of(2500, 'USD'),
            'stepStartAmount' => Money::of(2500, 'USD'),
            'stepEndAmount' => Money::of(2500 * $this->faker->numberBetween(1, 30), 'USD'),
            'isFirstOfBillingPeriod' => false,
            'createdAt' => CarbonImmutable::now()->subDay(),
            'updatedAt' => CarbonImmutable::now()->subDay(),
        ];
    }
}
