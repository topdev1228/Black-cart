<?php

declare(strict_types=1);

namespace App\Domain\Billings\Values;

use App\Domain\Shared\Values\Value;

class SubscriptionActivatedEvent extends Value
{
    public function __construct(
        public Subscription $subscription
    ) {
    }
}
