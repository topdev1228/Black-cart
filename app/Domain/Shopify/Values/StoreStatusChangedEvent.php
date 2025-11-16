<?php

declare(strict_types=1);

namespace App\Domain\Shopify\Values;

use App\Domain\Shared\Values\Value;
use App\Domain\Shopify\Enums\StoreStatus;

class StoreStatusChangedEvent extends Value
{
    public function __construct(
        public StoreStatus $status,
    ) {
    }
}
