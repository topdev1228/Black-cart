<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class FulfillmentOrderMergeUserErrorCode
{
    public const FULFILLMENT_ORDER_NOT_FOUND = 'FULFILLMENT_ORDER_NOT_FOUND';
    public const GREATER_THAN = 'GREATER_THAN';
    public const INVALID_LINE_ITEM_QUANTITY = 'INVALID_LINE_ITEM_QUANTITY';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
