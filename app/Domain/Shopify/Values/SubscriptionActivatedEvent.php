<?php

declare(strict_types=1);

namespace App\Domain\Shopify\Values;

use App\Domain\Shared\Values\Value;

class SubscriptionActivatedEvent extends Value
{
    public function __construct(
        public Subscription $subscription
    ) {
    }
}
