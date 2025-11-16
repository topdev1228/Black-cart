<?php
declare(strict_types=1);

namespace App\Domain\Shared\Traits;

use function class_exists;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Pagination\AbstractCursorPaginator;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Str;

trait HasModelCollection
{
    /**
     * @param static[] $items
     * @return Collection<array-key, static>|EloquentCollection<array-key, static>
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress ParentNotFound
     */
    public static function collection(Enumerable|AbstractPaginator|Paginator|AbstractCursorPaginator|CursorPaginator|array|null $items): Collection
    {
        $collection = (string) Str::of(static::class)
            ->replace('Models', 'Models\\Collections')
            ->append('Collection');

        if (!class_exists($collection)) {
            return new EloquentCollection($items);
        }

        return new $collection($items);
    }

    /**
     * @psalm-suppress MethodSignatureMismatch
     */
    public function newCollection(array|Enumerable|Collection $models = []): Collection
    {
        return static::collection($models);
    }
}
