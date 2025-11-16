<?php

declare(strict_types=1);

namespace App\Domain\Orders\Values\Factories;

use App\Domain\Shared\Values\Factory;
use Brick\Money\Money;
use Carbon\CarbonImmutable;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;

class WebhookPaymentSchedulesDueFactory extends Factory
{
    public function definition(): array
    {
        return [
            'paymentScheduleSourceId' => 'gid://shopify/PaymentSchedule/' . $this->faker->randomNumber(9),
            'paymentTermsId' => strval($this->faker->randomNumber(9)),
            'customerCurrency' => CurrencyAlpha3::US_Dollar,
            'customerAmount' => Money::ofMinor(10000, 'USD'),
            'dueAt' => CarbonImmutable::now(),
            'issuedAt' => null,
            'completedAt' => null,
        ];
    }
}
