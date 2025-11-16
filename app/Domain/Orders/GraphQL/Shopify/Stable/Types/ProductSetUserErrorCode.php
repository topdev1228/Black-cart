<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class ProductSetUserErrorCode
{
    public const CAPABILITY_VIOLATION = 'CAPABILITY_VIOLATION';
    public const DUPLICATED_OPTION_NAME = 'DUPLICATED_OPTION_NAME';
    public const DUPLICATED_OPTION_VALUE = 'DUPLICATED_OPTION_VALUE';
    public const GENERIC_ERROR = 'GENERIC_ERROR';
    public const GIFT_CARDS_NOT_ACTIVATED = 'GIFT_CARDS_NOT_ACTIVATED';
    public const GIFT_CARD_ATTRIBUTE_CANNOT_BE_CHANGED = 'GIFT_CARD_ATTRIBUTE_CANNOT_BE_CHANGED';
    public const INVALID_INPUT = 'INVALID_INPUT';
    public const INVALID_METAFIELD = 'INVALID_METAFIELD';
    public const INVALID_PRODUCT = 'INVALID_PRODUCT';
    public const INVALID_VARIANT = 'INVALID_VARIANT';
    public const JOB_ERROR = 'JOB_ERROR';
    public const OPTIONS_OVER_LIMIT = 'OPTIONS_OVER_LIMIT';
    public const OPTION_DOES_NOT_EXIST = 'OPTION_DOES_NOT_EXIST';
    public const OPTION_VALUES_MISSING = 'OPTION_VALUES_MISSING';
    public const OPTION_VALUES_OVER_LIMIT = 'OPTION_VALUES_OVER_LIMIT';
    public const OPTION_VALUE_DOES_NOT_EXIST = 'OPTION_VALUE_DOES_NOT_EXIST';
    public const PRODUCT_DOES_NOT_EXIST = 'PRODUCT_DOES_NOT_EXIST';
    public const PRODUCT_OPTIONS_INPUT_MISSING = 'PRODUCT_OPTIONS_INPUT_MISSING';
    public const PRODUCT_VARIANT_DOES_NOT_EXIST = 'PRODUCT_VARIANT_DOES_NOT_EXIST';
    public const VARIANTS_INPUT_MISSING = 'VARIANTS_INPUT_MISSING';
    public const VARIANTS_OVER_LIMIT = 'VARIANTS_OVER_LIMIT';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
