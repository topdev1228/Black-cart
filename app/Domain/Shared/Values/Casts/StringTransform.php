<?php
declare(strict_types=1);

namespace App\Domain\Shared\Values\Casts;

use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;
use Spatie\LaravelData\Support\Transformation\TransformationContext;
use Spatie\LaravelData\Transformers\Transformer;
use Str;

class StringTransform implements Transformer, Cast
{
    protected array $stringOperations;

    /**
     * @param string ...$stringOperation The string operations to perform on the value, use operation:arg1,arg2 to pass arguments to the operation
     */
    public function __construct(string ...$stringOperation)
    {
        $this->stringOperations = $stringOperation;
    }

    public function transform(DataProperty $property, mixed $value, TransformationContext $context): mixed
    {
        return $this->stringValue($value);
    }

    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): mixed
    {
        return $this->stringValue($value);
    }

    protected function stringValue(mixed $value): mixed
    {
        if (!is_scalar($value)) {
            return $value;
        }

        $value = Str::of($value);
        foreach ($this->stringOperations as $stringOperation) {
            $stringOperation = Str::of($stringOperation)->explode(':');
            $args = $stringOperation->map(fn ($op) => Str::of($op)->explode(',')->toArray())->flatten()->skip(1)->toArray();
            $value = $value->{$stringOperation->first()}(...$args);
        }

        return $value->toString();
    }
}
