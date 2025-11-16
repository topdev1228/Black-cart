<?php
declare(strict_types=1);

namespace Tests\Fixtures\Values;

use App\Domain\Shared\Traits\HasValueCollection;
use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Value;

class TestValueWithCollection extends Value
{
    use HasValueFactory;
    use HasValueCollection;

    public function __construct(
        public string $name,
    ) {
    }
}
