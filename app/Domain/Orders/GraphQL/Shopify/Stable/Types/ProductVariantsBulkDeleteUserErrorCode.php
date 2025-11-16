<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class ProductVariantsBulkDeleteUserErrorCode
{
    public const AT_LEAST_ONE_VARIANT_DOES_NOT_BELONG_TO_THE_PRODUCT = 'AT_LEAST_ONE_VARIANT_DOES_NOT_BELONG_TO_THE_PRODUCT';
    public const CANNOT_DELETE_LAST_VARIANT = 'CANNOT_DELETE_LAST_VARIANT';
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
