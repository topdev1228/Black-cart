<?php
declare(strict_types=1);

namespace App\Domain\Billings\Values;

use App\Domain\Shared\Values\Value;

/**
 * @psalm-suppress LessSpecificImplementedReturnType
 */
class StoreDeletedEvent extends Value
{
    public function __construct(
        public Store $store,
    ) {
    }
}
