<?php
declare(strict_types=1);

namespace App\Domain\Orders\Repositories;

use App\Domain\Orders\Models\TrialGroup as TrialGroupModel;
use App\Domain\Orders\Values\Order;
use App\Domain\Orders\Values\TrialGroup as TrialGroupValue;

class TrialGroupRepository
{
    public function getByOrder(Order $order): TrialGroupValue
    {
        return TrialGroupValue::from(
            TrialGroupModel::where(['order_id' => $order->id])->firstOrFail()
        );
    }

    public function createForOrder(Order $order): TrialGroupValue
    {
        $model = TrialGroupModel::create([
            'order_id' => $order->id,
        ]);

        return TrialGroupValue::from($model);
    }
}
