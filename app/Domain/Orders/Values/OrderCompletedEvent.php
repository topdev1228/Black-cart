<?php

declare(strict_types=1);

namespace App\Domain\Orders\Values;

use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Value;

class OrderCompletedEvent extends Value
{
    use HasValueFactory;

    public function __construct(
        public Order $order,
    ) {
    }
}
