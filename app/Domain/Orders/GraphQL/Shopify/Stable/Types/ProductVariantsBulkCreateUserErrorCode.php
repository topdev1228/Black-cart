<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class ProductVariantsBulkCreateUserErrorCode
{
    public const APP_NOT_AUTHORIZED = 'APP_NOT_AUTHORIZED';
    public const BLANK = 'BLANK';
    public const CAPABILITY_VIOLATION = 'CAPABILITY_VIOLATION';
    public const DISALLOWED_OWNER_TYPE = 'DISALLOWED_OWNER_TYPE';
    public const GREATER_THAN_OR_EQUAL_TO = 'GREATER_THAN_OR_EQUAL_TO';
    public const INCLUSION = 'INCLUSION';
    public const INVALID = 'INVALID';
    public const INVALID_TYPE = 'INVALID_TYPE';
    public const INVALID_VALUE = 'INVALID_VALUE';
    public const MUST_BE_FOR_THIS_PRODUCT = 'MUST_BE_FOR_THIS_PRODUCT';
    public const NEED_TO_ADD_OPTION_VALUES = 'NEED_TO_ADD_OPTION_VALUES';
    public const NEGATIVE_PRICE_VALUE = 'NEGATIVE_PRICE_VALUE';
    public const NOT_DEFINED_FOR_SHOP = 'NOT_DEFINED_FOR_SHOP';
    public const NO_KEY_ON_CREATE = 'NO_KEY_ON_CREATE';
    public const OPTION_VALUES_FOR_NUMBER_OF_UNKNOWN_OPTIONS = 'OPTION_VALUES_FOR_NUMBER_OF_UNKNOWN_OPTIONS';
    public const PRESENT = 'PRESENT';
    public const PRODUCT_DOES_NOT_EXIST = 'PRODUCT_DOES_NOT_EXIST';
    public const PRODUCT_SUSPENDED = 'PRODUCT_SUSPENDED';
    public const SUBSCRIPTION_VIOLATION = 'SUBSCRIPTION_VIOLATION';
    public const TAKEN = 'TAKEN';
    public const TOO_LONG = 'TOO_LONG';
    public const TOO_MANY_INVENTORY_LOCATIONS = 'TOO_MANY_INVENTORY_LOCATIONS';
    public const TOO_SHORT = 'TOO_SHORT';
    public const TRACKED_VARIANT_LOCATION_NOT_FOUND = 'TRACKED_VARIANT_LOCATION_NOT_FOUND';
    public const UNSTRUCTURED_RESERVED_NAMESPACE = 'UNSTRUCTURED_RESERVED_NAMESPACE';
    public const VARIANT_ALREADY_EXISTS = 'VARIANT_ALREADY_EXISTS';
    public const VARIANT_ALREADY_EXISTS_CHANGE_OPTION_VALUE = 'VARIANT_ALREADY_EXISTS_CHANGE_OPTION_VALUE';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
