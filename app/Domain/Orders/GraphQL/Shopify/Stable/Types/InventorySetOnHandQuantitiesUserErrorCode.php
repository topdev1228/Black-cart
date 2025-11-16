<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class InventorySetOnHandQuantitiesUserErrorCode
{
    public const INVALID_INVENTORY_ITEM = 'INVALID_INVENTORY_ITEM';
    public const INVALID_LOCATION = 'INVALID_LOCATION';
    public const INVALID_QUANTITY_NEGATIVE = 'INVALID_QUANTITY_NEGATIVE';
    public const INVALID_QUANTITY_TOO_HIGH = 'INVALID_QUANTITY_TOO_HIGH';
    public const INVALID_REASON = 'INVALID_REASON';
    public const INVALID_REFERENCE_DOCUMENT = 'INVALID_REFERENCE_DOCUMENT';
    public const ITEM_NOT_STOCKED_AT_LOCATION = 'ITEM_NOT_STOCKED_AT_LOCATION';
    public const NON_MUTABLE_INVENTORY_ITEM = 'NON_MUTABLE_INVENTORY_ITEM';
    public const SET_ON_HAND_QUANTITIES_FAILED = 'SET_ON_HAND_QUANTITIES_FAILED';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
