<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class ProductOptionsDeleteUserErrorCode
{
    public const CANNOT_DELETE_OPTION_WITH_MULTIPLE_VALUES = 'CANNOT_DELETE_OPTION_WITH_MULTIPLE_VALUES';
    public const CANNOT_MAKE_CHANGES_IF_VARIANT_IS_MISSING_REQUIRED_SKU = 'CANNOT_MAKE_CHANGES_IF_VARIANT_IS_MISSING_REQUIRED_SKU';
    public const CANNOT_USE_NON_DESTRUCTIVE_STRATEGY = 'CANNOT_USE_NON_DESTRUCTIVE_STRATEGY';
    public const OPTIONS_DO_NOT_BELONG_TO_THE_SAME_PRODUCT = 'OPTIONS_DO_NOT_BELONG_TO_THE_SAME_PRODUCT';
    public const OPTION_DOES_NOT_EXIST = 'OPTION_DOES_NOT_EXIST';
    public const PRODUCT_DOES_NOT_EXIST = 'PRODUCT_DOES_NOT_EXIST';
    public const PRODUCT_SUSPENDED = 'PRODUCT_SUSPENDED';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
