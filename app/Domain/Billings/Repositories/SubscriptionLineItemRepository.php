<?php
declare(strict_types=1);

namespace App\Domain\Billings\Repositories;

use App\Domain\Billings\Models\SubscriptionLineItem;
use App\Domain\Billings\Values\SubscriptionLineItem as SubscriptionLineItemValue;

class SubscriptionLineItemRepository
{
    public function store(SubscriptionLineItemValue $subscriptionLineItemValue): SubscriptionLineItemValue
    {
        return SubscriptionLineItemValue::from(SubscriptionLineItem::create($subscriptionLineItemValue->toArray()));
    }
}
