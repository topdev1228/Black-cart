<?php
declare(strict_types=1);

namespace Tests\Fixtures\Values\Events;

use App\Domain\Billings\Values\Store;
use App\Domain\Shared\Values\Value;

class StoreCreatedEvent extends Value
{
    public function __construct(
        public Store $store,
    ) {
    }
}
