<?php
declare(strict_types=1);

namespace App\Domain\Orders\Repositories;

use App\Domain\Orders\Enums\LineItemStatus;
use App\Domain\Orders\Models\LineItem as LineItemModel;
use App\Domain\Orders\Values\Collections\LineItemCollection;
use App\Domain\Orders\Values\LineItem as LineItemValue;
use Str;

class LineItemRepository
{
    public function getById(string $id): LineItemValue
    {
        $lineItem = LineItemModel::findOrFail($id);

        return LineItemValue::from($lineItem);
    }

    public function getBySourceId(string $sourceId): LineItemValue
    {
        return LineItemValue::from(LineItemModel::where([
            'source_id' => Str::shopifyGid($sourceId, 'LineItem'),
        ])->firstOrFail());
    }

    public function getByTrialGroupId(string $trialGroupId): LineItemValue
    {
        return LineItemValue::from(LineItemModel::where('trial_group_id', $trialGroupId)->firstOrFail());
    }

    public function update(LineItemValue $value): LineItemValue
    {
        $lineItem = LineItemModel::findOrFail($value->id);
        $lineItem->update($value->toArray());

        return LineItemValue::from($lineItem);
    }

    public function create(LineItemValue $value): LineItemValue
    {
        return LineItemValue::from(LineItemModel::create($value->toArray()));
    }

    public function save(LineItemValue $value): LineItemValue
    {
        if ($value->id) {
            return $this->update($value);
        }

        return $this->create($value);
    }

    /**
     * @psalm-suppress InvalidReturnType
     * @psalm-suppress InvalidReturnStatement
     */
    public function all(): LineItemCollection
    {
        /** @psalm-suppress InvalidArgument */
        return LineItemValue::collection(LineItemModel::all());
    }

    public function getByStatus(string $orderId, LineItemStatus $status): LineItemCollection
    {
        return LineItemValue::collection(LineItemModel::where('order_id', $orderId)->where('status', $status)->get());
    }
}
