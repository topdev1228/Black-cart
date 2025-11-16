<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class ProductVariantsBulkCreateStrategy
{
    public const DEFAULT = 'DEFAULT';
    public const REMOVE_STANDALONE_VARIANT = 'REMOVE_STANDALONE_VARIANT';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
