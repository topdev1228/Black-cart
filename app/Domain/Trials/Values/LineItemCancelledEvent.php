<?php

declare(strict_types=1);

namespace App\Domain\Trials\Values;

use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Value;

class LineItemCancelledEvent extends Value
{
    use HasValueFactory;

    public function __construct(
        public string $lineItemId
    ) {
    }
}
