<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class FulfillmentHoldReason
{
    public const AWAITING_PAYMENT = 'AWAITING_PAYMENT';
    public const AWAITING_RETURN_ITEMS = 'AWAITING_RETURN_ITEMS';
    public const HIGH_RISK_OF_FRAUD = 'HIGH_RISK_OF_FRAUD';
    public const INCORRECT_ADDRESS = 'INCORRECT_ADDRESS';
    public const INVENTORY_OUT_OF_STOCK = 'INVENTORY_OUT_OF_STOCK';
    public const ONLINE_STORE_POST_PURCHASE_CROSS_SELL = 'ONLINE_STORE_POST_PURCHASE_CROSS_SELL';
    public const OTHER = 'OTHER';
    public const UNKNOWN_DELIVERY_DATE = 'UNKNOWN_DELIVERY_DATE';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
