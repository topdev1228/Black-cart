<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class DeliveryLegacyModeBlockedReason
{
    public const MULTI_LOCATION_DISABLED = 'MULTI_LOCATION_DISABLED';
    public const NO_LOCATIONS_FULFILLING_ONLINE_ORDERS = 'NO_LOCATIONS_FULFILLING_ONLINE_ORDERS';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
