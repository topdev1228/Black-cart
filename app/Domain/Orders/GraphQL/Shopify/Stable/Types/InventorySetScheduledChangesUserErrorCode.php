<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class InventorySetScheduledChangesUserErrorCode
{
    public const DUPLICATE_FROM_NAME = 'DUPLICATE_FROM_NAME';
    public const DUPLICATE_TO_NAME = 'DUPLICATE_TO_NAME';
    public const ERROR_UPDATING_SCHEDULED = 'ERROR_UPDATING_SCHEDULED';
    public const INCLUSION = 'INCLUSION';
    public const INVALID_FROM_NAME = 'INVALID_FROM_NAME';
    public const INVALID_REASON = 'INVALID_REASON';
    public const INVALID_TO_NAME = 'INVALID_TO_NAME';
    public const INVENTORY_ITEM_NOT_FOUND = 'INVENTORY_ITEM_NOT_FOUND';
    public const INVENTORY_STATE_NOT_FOUND = 'INVENTORY_STATE_NOT_FOUND';
    public const ITEMS_EMPTY = 'ITEMS_EMPTY';
    public const LOCATION_NOT_FOUND = 'LOCATION_NOT_FOUND';
    public const SAME_FROM_TO_NAMES = 'SAME_FROM_TO_NAMES';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
