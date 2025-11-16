<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class FulfillmentOrderLineItemsPreparedForPickupUserErrorCode
{
    public const FULFILLMENT_ORDER_INVALID = 'FULFILLMENT_ORDER_INVALID';
    public const NO_LINE_ITEMS_TO_PREPARE_FOR_FULFILLMENT_ORDER = 'NO_LINE_ITEMS_TO_PREPARE_FOR_FULFILLMENT_ORDER';
    public const UNABLE_TO_PREPARE_QUANTITY = 'UNABLE_TO_PREPARE_QUANTITY';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
