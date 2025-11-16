<?php

declare(strict_types=1);

namespace App\Domain\Orders\Values\Factories;

use App\Domain\Orders\Values\WebhookRefundsCreateDuty;
use App\Domain\Shared\Values\Factory;

class WebhookRefundsCreateDutiesFactory extends Factory
{
    public function definition(): array
    {
        return [
            'duties' => WebhookRefundsCreateDuty::collection([WebhookRefundsCreateDuty::builder()->create()]),
        ];
    }
}
