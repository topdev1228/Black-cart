<?php
declare(strict_types=1);

namespace App\Domain\Billings\Values;

use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Value;

/**
 * @psalm-suppress LessSpecificImplementedReturnType
 */
class Store extends Value
{
    use HasValueFactory;

    public function __construct(
        public string $id = '',
        public string $name = '',
        public string $domain = '',
    ) {
    }
}
