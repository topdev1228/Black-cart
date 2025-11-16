<?php
declare(strict_types=1);

namespace App\Domain\Trials\Repositories;

use App\Domain\Trials\Enums\TrialStatus;
use App\Domain\Trials\Models\Trialable;
use App\Domain\Trials\Values\Collections\TrialableCollection;
use App\Domain\Trials\Values\Trialable as TrialableValue;
use Illuminate\Database\Eloquent\Collection;

class TrialableRepository
{
    public function __construct()
    {
    }

    public function all(): Collection
    {
        return Trialable::all();
    }

    public function getById(string $id): TrialableValue
    {
        $model = Trialable::findOrFail($id);

        return TrialableValue::from($model);
    }

    public function getOne(array $params): ?TrialableValue
    {
        $trialable = Trialable::where($params)->first();

        return $trialable ? TrialableValue::from($trialable) : null;
    }

    /**
     * @psalm-suppress InvalidReturnType
     * @psalm-suppress InvalidReturnStatement
     */
    public function getAll(array $params, bool $includeCancelled = false): TrialableCollection
    {
        $items = Trialable::where($params)->when(!$includeCancelled, function ($query) {
            return $query->whereNot('status', TrialStatus::CANCELLED);
        })->get();

        return TrialableValue::collection($items);
    }

    public function getBySource(string $sourceId, string $sourceKey = null): TrialableValue
    {
        $model = Trialable::where([
            'source_id' => $sourceId,
            'source_key' => $sourceKey,
        ])->firstOrFail();

        return TrialableValue::from($model);
    }

    public function save(TrialableValue $value): TrialableValue
    {
        $model = $value->id ? Trialable::findOrFail($value->id) : new Trialable();
        $model->fill($value->toArray());
        $model->save();

        return TrialableValue::from($model);
    }
}
