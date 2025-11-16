<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class LocationDeleteUserErrorCode
{
    public const GENERIC_ERROR = 'GENERIC_ERROR';
    public const LOCATION_HAS_ACTIVE_RETAIL_SUBSCRIPTION = 'LOCATION_HAS_ACTIVE_RETAIL_SUBSCRIPTION';
    public const LOCATION_HAS_INVENTORY = 'LOCATION_HAS_INVENTORY';
    public const LOCATION_HAS_PENDING_ORDERS = 'LOCATION_HAS_PENDING_ORDERS';
    public const LOCATION_IS_ACTIVE = 'LOCATION_IS_ACTIVE';
    public const LOCATION_NOT_FOUND = 'LOCATION_NOT_FOUND';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
