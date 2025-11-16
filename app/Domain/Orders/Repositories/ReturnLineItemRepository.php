<?php
declare(strict_types=1);

namespace App\Domain\Orders\Repositories;

use App\Domain\Orders\Models\ReturnLineItem;
use App\Domain\Orders\Values\ReturnLineItem as ReturnLineItemValue;

class ReturnLineItemRepository
{
    public function __construct()
    {
    }

    public function save(ReturnLineItemValue $returnLineItem): ReturnLineItemValue
    {
        return ReturnLineItemValue::from(ReturnLineItem::updateOrCreate(['source_id' => $returnLineItem->sourceId], $returnLineItem->toArray()));
    }
}
