<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class FulfillmentOrderAction
{
    public const CANCEL_FULFILLMENT_ORDER = 'CANCEL_FULFILLMENT_ORDER';
    public const CREATE_FULFILLMENT = 'CREATE_FULFILLMENT';
    public const EXTERNAL = 'EXTERNAL';
    public const HOLD = 'HOLD';
    public const MARK_AS_OPEN = 'MARK_AS_OPEN';
    public const MERGE = 'MERGE';
    public const MOVE = 'MOVE';
    public const RELEASE_HOLD = 'RELEASE_HOLD';
    public const REQUEST_CANCELLATION = 'REQUEST_CANCELLATION';
    public const REQUEST_FULFILLMENT = 'REQUEST_FULFILLMENT';
    public const SPLIT = 'SPLIT';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
