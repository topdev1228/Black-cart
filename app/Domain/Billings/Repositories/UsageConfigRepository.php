<?php
declare(strict_types=1);

namespace App\Domain\Billings\Repositories;

use App\Domain\Billings\Models\UsageConfig;
use App\Domain\Billings\Values\UsageConfig as UsageConfigValue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Date;

class UsageConfigRepository
{
    public function create(UsageConfigValue $value): UsageConfigValue
    {
        return UsageConfigValue::from(UsageConfig::create($value->toArray()));
    }

    public function get(): UsageConfigValue
    {
        return UsageConfigValue::from(UsageConfig::where('valid_from', '<=', Date::now())
            ->where(function (Builder $query) {
                $query->where('valid_to', '>=', Date::now())
                    ->orWhereNull('valid_to');
            })
            ->orderBy('valid_from', 'desc')
            ->first());
    }
}
