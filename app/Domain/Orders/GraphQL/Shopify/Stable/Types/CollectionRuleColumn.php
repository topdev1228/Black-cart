<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class CollectionRuleColumn
{
    public const IS_PRICE_REDUCED = 'IS_PRICE_REDUCED';
    public const PRODUCT_METAFIELD_DEFINITION = 'PRODUCT_METAFIELD_DEFINITION';
    public const PRODUCT_TAXONOMY_NODE_ID = 'PRODUCT_TAXONOMY_NODE_ID';
    public const TAG = 'TAG';
    public const TITLE = 'TITLE';
    public const TYPE = 'TYPE';
    public const VARIANT_COMPARE_AT_PRICE = 'VARIANT_COMPARE_AT_PRICE';
    public const VARIANT_INVENTORY = 'VARIANT_INVENTORY';
    public const VARIANT_METAFIELD_DEFINITION = 'VARIANT_METAFIELD_DEFINITION';
    public const VARIANT_PRICE = 'VARIANT_PRICE';
    public const VARIANT_TITLE = 'VARIANT_TITLE';
    public const VARIANT_WEIGHT = 'VARIANT_WEIGHT';
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
