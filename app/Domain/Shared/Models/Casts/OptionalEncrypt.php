<?php
declare(strict_types=1);

namespace App\Domain\Shared\Models\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class OptionalEncrypt implements CastsAttributes
{
    /**
     * @param array<array-key, mixed> $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($model->{$model::secureColumn()}) {
            return decrypt($value);
        }

        return $value;
    }

    /**
     * @param array<array-key, mixed> $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): array
    {
        if ($model->{$model::secureColumn()}) {
            return [
                $key => encrypt($value),
            ];
        }

        return [
            $key => $value,
        ];
    }
}
