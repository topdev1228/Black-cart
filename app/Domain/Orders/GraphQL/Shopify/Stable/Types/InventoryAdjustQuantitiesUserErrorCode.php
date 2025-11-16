<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class InventoryAdjustQuantitiesUserErrorCode
{
    public const ADJUST_QUANTITIES_FAILED = 'ADJUST_QUANTITIES_FAILED';
    public const INTERNAL_LEDGER_DOCUMENT = 'INTERNAL_LEDGER_DOCUMENT';
    public const INVALID_AVAILABLE_DOCUMENT = 'INVALID_AVAILABLE_DOCUMENT';
    public const INVALID_INVENTORY_ITEM = 'INVALID_INVENTORY_ITEM';
    public const INVALID_LEDGER_DOCUMENT = 'INVALID_LEDGER_DOCUMENT';
    public const INVALID_LOCATION = 'INVALID_LOCATION';
    public const INVALID_QUANTITY_DOCUMENT = 'INVALID_QUANTITY_DOCUMENT';
    public const INVALID_QUANTITY_NAME = 'INVALID_QUANTITY_NAME';
    public const INVALID_QUANTITY_TOO_HIGH = 'INVALID_QUANTITY_TOO_HIGH';
    public const INVALID_QUANTITY_TOO_LOW = 'INVALID_QUANTITY_TOO_LOW';
    public const INVALID_REASON = 'INVALID_REASON';
    public const INVALID_REFERENCE_DOCUMENT = 'INVALID_REFERENCE_DOCUMENT';
    public const ITEM_NOT_STOCKED_AT_LOCATION = 'ITEM_NOT_STOCKED_AT_LOCATION';
    public const MAX_ONE_LEDGER_DOCUMENT = 'MAX_ONE_LEDGER_DOCUMENT';
    public const NON_MUTABLE_INVENTORY_ITEM = 'NON_MUTABLE_INVENTORY_ITEM';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
