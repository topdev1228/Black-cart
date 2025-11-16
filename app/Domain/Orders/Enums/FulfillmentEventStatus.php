<?php
declare(strict_types=1);

namespace App\Domain\Orders\Enums;

enum FulfillmentEventStatus: string
{
    case ATTEMPTED_DELIVERY = 'attempted_delivery';
    case CARRIER_PICKUP = 'carrier_picked_up';
    case CONFIRMED = 'confirmed';
    case DELAYED = 'delayed';
    case DELIVERED = 'delivered';
    case FAILURE = 'failure';
    case IN_TRANSIT = 'in_transit';
    case LABEL_PRINTED = 'label_printed';
    case LABEL_PURCHASED = 'label_purchased';
    case OUT_FOR_DELIVERY = 'out_for_delivery';
    case PICKED_UP = 'picked_up';
    case READY_FOR_PICKUP = 'ready_for_pickup';
}
