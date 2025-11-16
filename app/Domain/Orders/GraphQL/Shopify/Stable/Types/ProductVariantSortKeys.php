<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class ProductVariantSortKeys
{
    public const FULL_TITLE = 'FULL_TITLE';
    public const ID = 'ID';
    public const INVENTORY_LEVELS_AVAILABLE = 'INVENTORY_LEVELS_AVAILABLE';
    public const INVENTORY_MANAGEMENT = 'INVENTORY_MANAGEMENT';
    public const INVENTORY_POLICY = 'INVENTORY_POLICY';
    public const INVENTORY_QUANTITY = 'INVENTORY_QUANTITY';
    public const NAME = 'NAME';
    public const POPULAR = 'POPULAR';
    public const POSITION = 'POSITION';
    public const RELEVANCE = 'RELEVANCE';
    public const SKU = 'SKU';
    public const TITLE = 'TITLE';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
