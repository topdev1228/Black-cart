<?php
declare(strict_types=1);

namespace App\Domain\Shared\Traits;

use App\Domain\Shared\Exceptions\UndefinedPropertyException;

trait Strict
{
    public function __get(string $name): mixed
    {
        throw new UndefinedPropertyException($name, static::class);
    }

    public function __set(string $name, mixed $value): void
    {
        throw new UndefinedPropertyException($name, static::class);
    }
}
