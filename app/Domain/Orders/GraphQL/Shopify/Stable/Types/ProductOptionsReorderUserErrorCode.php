<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class ProductOptionsReorderUserErrorCode
{
    public const CANNOT_MAKE_CHANGES_IF_VARIANT_IS_MISSING_REQUIRED_SKU = 'CANNOT_MAKE_CHANGES_IF_VARIANT_IS_MISSING_REQUIRED_SKU';
    public const DUPLICATED_OPTION_NAME = 'DUPLICATED_OPTION_NAME';
    public const DUPLICATED_OPTION_VALUE = 'DUPLICATED_OPTION_VALUE';
    public const MISSING_OPTION_NAME = 'MISSING_OPTION_NAME';
    public const MISSING_OPTION_VALUE = 'MISSING_OPTION_VALUE';
    public const MIXING_ID_AND_NAME_KEYS_IS_NOT_ALLOWED = 'MIXING_ID_AND_NAME_KEYS_IS_NOT_ALLOWED';
    public const NO_KEY_ON_REORDER = 'NO_KEY_ON_REORDER';
    public const OPTION_ID_DOES_NOT_EXIST = 'OPTION_ID_DOES_NOT_EXIST';
    public const OPTION_NAME_DOES_NOT_EXIST = 'OPTION_NAME_DOES_NOT_EXIST';
    public const OPTION_VALUE_DOES_NOT_EXIST = 'OPTION_VALUE_DOES_NOT_EXIST';
    public const OPTION_VALUE_ID_DOES_NOT_EXIST = 'OPTION_VALUE_ID_DOES_NOT_EXIST';
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
