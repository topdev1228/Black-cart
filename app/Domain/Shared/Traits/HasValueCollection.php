<?php
declare(strict_types=1);

namespace App\Domain\Shared\Traits;

use App\Domain\Shared\Values\Value;
use function class_exists;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Pagination\AbstractCursorPaginator;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use ReflectionClass;
use Spatie\LaravelData\Contracts\BaseData;
use Spatie\LaravelData\CursorPaginatedDataCollection;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\PaginatedDataCollection;
use Str;

trait HasValueCollection
{
    /**
     * @psalm-param CursorPaginator|Paginator|AbstractCursorPaginator|AbstractPaginator|Enumerable|DataCollection|array<array-key, mixed>|null $items
     * @psalm-return CursorPaginatedDataCollection<array-key, BaseData&static>|DataCollection<array-key, BaseData&static>|PaginatedDataCollection<array-key, BaseData&static>
     * @psalm-suppress ParentNotFound
     * @psalm-suppress MoreSpecificImplementedParamType
     * @psalm-suppress InvalidReturnType
     * @psalm-suppress TypeDoesNotContainType
     * @psalm-suppress MoreSpecificReturnType
     */
    public static function collection(Enumerable|AbstractPaginator|Paginator|AbstractCursorPaginator|CursorPaginator|DataCollection|array|null $items): DataCollection|CursorPaginatedDataCollection|PaginatedDataCollection
    {
        $collection = (string) Str::of(static::class)
            ->replace(['Models', 'Values'], ['Models\\Collections', 'Values\\Collections'])
            ->append('Collection');

        if (!class_exists($collection)) {
            return self::genericCollection($items);
        }

        $collectionClass = new ReflectionClass($collection);

        if (is_a($collection, DataCollection::class, true)) {
            return $collectionClass->newInstance(static::class, $items);
        }

        return $collectionClass->newInstance($items);
    }

    /**
     * @psalm-return CursorPaginatedDataCollection<Value>|DataCollection<Value>|PaginatedDataCollection<Value>
     * @psalm-suppress InvalidReturnType
     * @psalm-suppress MismatchingDocblockReturnType
     * @psalm-suppress MoreSpecificReturnType
     */
    public function newCollection(array|Enumerable|DataCollection|Collection $items = []): DataCollection|CursorPaginatedDataCollection|PaginatedDataCollection
    {
        /** @psalm-suppress PossiblyInvalidArgument */
        return static::collection($items);
    }

    /**
     * @psalm-suppress MoreSpecificReturnType
     */
    protected static function genericCollection(array|Paginator|Enumerable|AbstractCursorPaginator|DataCollection|AbstractPaginator|CursorPaginator|null $items): DataCollection|AbstractPaginator|AbstractCursorPaginator|CursorPaginator|Paginator
    {
        if ($items instanceof Paginator || $items instanceof AbstractPaginator) {
            /** @psalm-suppress ArgumentTypeCoercion */
            $collectionClass = new ReflectionClass(static::$_paginatedCollectionClass);

            return $collectionClass->newInstance(static::class, $items);
        }

        if ($items instanceof AbstractCursorPaginator || $items instanceof CursorPaginator) {
            /** @psalm-suppress ArgumentTypeCoercion */
            $collectionClass = new ReflectionClass(static::$_cursorPaginatedCollectionClass);

            return $collectionClass->newInstance(static::class, $items);
        }

        return new DataCollection(static::class, $items);
    }
}
