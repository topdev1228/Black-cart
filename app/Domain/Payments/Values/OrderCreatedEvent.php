<?php
declare(strict_types=1);

namespace App\Domain\Payments\Values;

use App\Domain\Shared\Traits\HasValueCollection;
use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Value;

class OrderCreatedEvent extends Value
{
    use HasValueFactory;
    use HasValueCollection;

    public function __construct(public Order $order)
    {
    }
}
