<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class ProductSortKeys
{
    public const CREATED_AT = 'CREATED_AT';
    public const ID = 'ID';
    public const INVENTORY_TOTAL = 'INVENTORY_TOTAL';
    public const PRODUCT_TYPE = 'PRODUCT_TYPE';
    public const PUBLISHED_AT = 'PUBLISHED_AT';
    public const RELEVANCE = 'RELEVANCE';
    public const TITLE = 'TITLE';
    public const UPDATED_AT = 'UPDATED_AT';
    public const VENDOR = 'VENDOR';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
