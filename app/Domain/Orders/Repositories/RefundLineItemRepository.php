<?php
declare(strict_types=1);

namespace App\Domain\Orders\Repositories;

use App\Domain\Orders\Models\RefundLineItem;
use App\Domain\Orders\Values\RefundLineItem as RefundLineItemValue;

class RefundLineItemRepository
{
    public function create(RefundLineItemValue $refundLineItem)
    {
        return RefundLineItemValue::from(RefundLineItem::create($refundLineItem->toArray()));
    }
}
