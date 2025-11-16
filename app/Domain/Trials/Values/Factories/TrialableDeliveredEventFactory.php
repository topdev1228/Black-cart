<?php

declare(strict_types=1);

namespace App\Domain\Trials\Values\Factories;

use App\Domain\Shared\Values\Factory;
use App\Domain\Trials\Values\Trialable;

class TrialableDeliveredEventFactory extends Factory
{
    public function definition(): array
    {
        return [
            'trialable' => Trialable::builder()->create(),
        ];
    }
}
