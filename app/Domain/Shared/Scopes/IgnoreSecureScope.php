<?php
declare(strict_types=1);

namespace App\Domain\Shared\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class IgnoreSecureScope implements Scope
{
    public function __construct(protected string $column)
    {
    }

    public function apply(Builder $builder, Model $model)
    {
        $builder->where($this->column, '!=', 1);
    }
}
