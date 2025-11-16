<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class FulfillmentEventStatus
{
    public const ATTEMPTED_DELIVERY = 'ATTEMPTED_DELIVERY';
    public const CARRIER_PICKED_UP = 'CARRIER_PICKED_UP';
    public const CONFIRMED = 'CONFIRMED';
    public const DELAYED = 'DELAYED';
    public const DELIVERED = 'DELIVERED';
    public const FAILURE = 'FAILURE';
    public const IN_TRANSIT = 'IN_TRANSIT';
    public const LABEL_PRINTED = 'LABEL_PRINTED';
    public const LABEL_PURCHASED = 'LABEL_PURCHASED';
    public const OUT_FOR_DELIVERY = 'OUT_FOR_DELIVERY';
    public const PICKED_UP = 'PICKED_UP';
    public const READY_FOR_PICKUP = 'READY_FOR_PICKUP';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
