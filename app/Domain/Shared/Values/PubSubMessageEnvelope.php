<?php

declare(strict_types=1);

namespace App\Domain\Shared\Values;

use App\Domain\Shared\Traits\HasValueFactory;

class PubSubMessageEnvelope extends Value
{
    use HasValueFactory;

    public function __construct(
        public string $subscription,
        public PubSubMessage $message,
    ) {
    }
}
