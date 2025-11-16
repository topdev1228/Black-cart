<?php
declare(strict_types=1);

namespace App\Domain\Shared\Values\Casts;

use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;
use ValueError;

class SafeEnum implements Cast
{
    protected array $stringOperations;

    public function __construct(string ...$stringOperation)
    {
        $this->stringOperations = $stringOperation;
    }

    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): mixed
    {
        if (!is_string($value)) {
            return $value;
        }

        $value = (new StringTransform(...$this->stringOperations))->cast($property, $value, $properties, $context);
        /** @psalm-var enum-string $enumType */
        $enumType = array_keys($property->type->getAcceptedTypes())[0];
        $result = $enumType::tryFrom($value);
        if ($result === null && $property->hasDefaultValue) {
            return $property->defaultValue;
        }

        if ($result === null && $property->type->isNullable) {
            return null;
        }

        if ($result === null) {
            throw new ValueError(sprintf('"%s" is not a valid backing value for enum "%s"', $value, $enumType));
        }

        return $result;
    }
}
