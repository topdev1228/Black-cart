<?php
declare(strict_types=1);

namespace App\Domain\Orders\Repositories;

use App\Domain\Orders\Models\OrderReturn;
use App\Domain\Orders\Values\OrderReturn as ReturnValue;

class ReturnRepository
{
    public function __construct()
    {
    }

    public function save(ReturnValue $return): ReturnValue
    {
        return ReturnValue::from(OrderReturn::updateOrCreate(['source_id' => $return->sourceId], $return->toArray()));
    }

    public function getBySourceId(string $sourceId): ReturnValue
    {
        return ReturnValue::from(OrderReturn::where(['source_id' => $sourceId])->firstOrFail());
    }
}
