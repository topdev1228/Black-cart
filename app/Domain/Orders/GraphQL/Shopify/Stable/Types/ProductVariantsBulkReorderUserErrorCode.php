<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class ProductVariantsBulkReorderUserErrorCode
{
    public const DUPLICATED_VARIANT_ID = 'DUPLICATED_VARIANT_ID';
    public const INVALID_POSITION = 'INVALID_POSITION';
    public const MISSING_VARIANT = 'MISSING_VARIANT';
    public const PRODUCT_DOES_NOT_EXIST = 'PRODUCT_DOES_NOT_EXIST';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
