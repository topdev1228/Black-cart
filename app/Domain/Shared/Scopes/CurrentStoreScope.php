<?php
declare(strict_types=1);

namespace App\Domain\Shared\Scopes;

use App\Domain\Stores\Exceptions\MissingStoreContextException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\App;

class CurrentStoreScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        if (App::context()->store->id === null) {
            throw new MissingStoreContextException('No store context set.');
        }

        $builder->where($model->getQualifiedStoreIdColumn(), '=', App::context()->store->id);
    }
}
