<?php
declare(strict_types=1);

namespace App\Domain\Stores\Values\Casts;

use App\Domain\Shared\Values\Value;
use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;
use Spatie\LaravelData\Support\Transformation\TransformationContext;
use Spatie\LaravelData\Transformers\Transformer;

class KeyValuePair implements Cast, Transformer
{
    public function transform(DataProperty $property, mixed $value, TransformationContext $context): mixed
    {
        $return = [];
        foreach ($value as $storeSetting) {
            $return[$storeSetting->name] = $storeSetting->toArray();
        }

        return $return;
    }

    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): mixed
    {
        /** @var class-string<Value> $valueClass */
        $valueClass = $property->type->dataClass;
        $values = [];
        foreach ($value as $k => $v) {
            if (is_string($v)) {
                $values[] = ['name' => $k, 'value' => $v];
            }
            if ($v instanceof Model || $v instanceof Value) {
                $values[] = $v->toArray();
            }
            if (is_array($v)) {
                $values[] = $v;
            }
        }

        return $valueClass::collection($values);
    }
}
