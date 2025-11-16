<?php
declare(strict_types=1);

namespace App\Domain\Shopify\Values;

use App\Domain\Shared\Traits\HasValueCollection;
use App\Domain\Shared\Traits\HasValueFactory;
use App\Domain\Shared\Values\Value;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class Metafield extends Value
{
    use HasValueCollection;
    use HasValueFactory;

    public function __construct(
        public string $key,
        public string $value,
        public string $type,
        public ?string $id = null,
    ) {
    }
}
