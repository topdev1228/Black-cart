<?php

declare(strict_types=1);

namespace App\Domain\Orders\Values;

use App\Domain\Shared\Values\Value;

class InitialAuthFailedEvent extends Value
{
    public function __construct(public string $orderId)
    {
    }
}
