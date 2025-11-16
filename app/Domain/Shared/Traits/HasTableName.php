<?php
declare(strict_types=1);

namespace App\Domain\Shared\Traits;

trait HasTableName
{
    public static ?string $tableName = null;

    public static function table(): string
    {
        return static::$tableName ?? (new static())->getTable();
    }

    public static function col(string $column): string
    {
        return static::table() . '.' . $column;
    }
}
