<?php
declare(strict_types=1);

namespace App\Domain\Shared\Traits;

use App\Domain\Shared\Scopes\CurrentStoreScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 */
trait CurrentStore
{
    /**
     * Boot the current store trait for a model.
     *
     * @return void
     */
    public static function bootCurrentStore()
    {
        static::addGlobalScope(new CurrentStoreScope());
    }

    public function scopeWithoutCurrentStore(Builder $query): Builder
    {
        $query->withoutGlobalScopes([CurrentStoreScope::class]);

        return $query;
    }

    /**
     * Get the name of the "store id" column.
     *
     * @return string
     */
    public function getStoreIdColumn()
    {
        return defined(static::class . '::STORE_ID') ? static::STORE_ID : 'store_id';
    }

    /**
     * Get the fully qualified "store id" column.
     *
     * @return string
     */
    public function getQualifiedStoreIdColumn()
    {
        return $this->qualifyColumn($this->getStoreIdColumn());
    }
}
