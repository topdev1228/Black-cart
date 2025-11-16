<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class InventoryBulkToggleActivationUserErrorCode
{
    public const CANNOT_DEACTIVATE_FROM_ONLY_LOCATION = 'CANNOT_DEACTIVATE_FROM_ONLY_LOCATION';
    public const COMMITTED_AND_INCOMING_INVENTORY_AT_LOCATION = 'COMMITTED_AND_INCOMING_INVENTORY_AT_LOCATION';
    public const COMMITTED_INVENTORY_AT_LOCATION = 'COMMITTED_INVENTORY_AT_LOCATION';
    public const FAILED_TO_STOCK_AT_LOCATION = 'FAILED_TO_STOCK_AT_LOCATION';
    public const FAILED_TO_UNSTOCK_FROM_LOCATION = 'FAILED_TO_UNSTOCK_FROM_LOCATION';
    public const GENERIC_ERROR = 'GENERIC_ERROR';
    public const INCOMING_INVENTORY_AT_LOCATION = 'INCOMING_INVENTORY_AT_LOCATION';
    public const INVENTORY_ITEM_NOT_FOUND = 'INVENTORY_ITEM_NOT_FOUND';
    public const INVENTORY_MANAGED_BY_3RD_PARTY = 'INVENTORY_MANAGED_BY_3RD_PARTY';
    public const INVENTORY_MANAGED_BY_SHOPIFY = 'INVENTORY_MANAGED_BY_SHOPIFY';
    public const LOCATION_NOT_FOUND = 'LOCATION_NOT_FOUND';
    public const MISSING_SKU = 'MISSING_SKU';
    public const RESERVED_INVENTORY_AT_LOCATION = 'RESERVED_INVENTORY_AT_LOCATION';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
