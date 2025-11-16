<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class FulfillmentOrderMerchantRequestKind
{
    public const CANCELLATION_REQUEST = 'CANCELLATION_REQUEST';
    public const FULFILLMENT_REQUEST = 'FULFILLMENT_REQUEST';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
