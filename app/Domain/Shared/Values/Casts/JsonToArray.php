<?php
declare(strict_types=1);

namespace App\Domain\Shared\Values\Casts;

use InvalidArgumentException;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

class JsonToArray implements Cast
{
    public function __construct()
    {
    }

    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): mixed
    {
        if (!is_string($value) && !is_array($value)) {
            throw new InvalidArgumentException('Value must be an array or a string');
        }

        if (is_array($value)) {
            return $value;
        }

        if (strlen($value) === 0) {
            return [];
        }

        return json_decode($value, true, 512, JSON_THROW_ON_ERROR);
    }
}
