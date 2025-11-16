<?php

declare(strict_types=1);

namespace App\Domain\Orders\Values\Factories;

use App\Domain\Orders\Values\Refund;
use App\Domain\Shared\Values\Factory;

class RefundCreatedEventFactory extends Factory
{
    public function definition(): array
    {
        return [
            'refund' => Refund::builder()->create(),
        ];
    }
}
