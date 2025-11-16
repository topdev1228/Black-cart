<?php

declare(strict_types=1);

namespace App\Domain\Billings\Values\Factories;

use App\Domain\Shared\Values\Factory;
use Illuminate\Support\Facades\Date;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;

class UsageConfigFactory extends Factory
{
    public function definition(): array
    {
        return [
            'storeId' => $this->faker->uuid(),
            'subscriptionLineItemId' => $this->faker->uuid(),
            'description' => $this->faker->sentence(),
            'config' => json_decode(<<<'EOF'
                    [
                      {
                        "description": "First $2500 included in subscription fee",
                        "start": 0,
                        "end": 250000,
                        "step": 250000,
                        "price": 0,
                        "currency": "USD"
                      },
                      {
                        "description": "$2500.01 - $100,000 at $100/$2500 (4%)",
                        "start": 250001,
                        "end": 10000000,
                        "step": 250000,
                        "price": 10000,
                        "currency": "USD"
                      },
                      {
                        "description": ">$200,000.01 at $50/$2500 (2%)",
                        "start": 20000001,
                        "end": null,
                        "step": 250000,
                        "price": 5000,
                        "currency": "USD"
                      },
                      {
                        "description": "$100,000.01 - $200,000 at $800/$25000 (3.2%)",
                        "start": 10000001,
                        "end": 20000000,
                        "step": 2500000,
                        "price": 80000,
                        "currency": "USD"
                      }
                    ]
                    EOF, true, 512, JSON_THROW_ON_ERROR),
            'currency' => CurrencyAlpha3::US_Dollar,
            'validFrom' => Date::now()->subDay(),
            'validTo' => null,
        ];
    }
}
