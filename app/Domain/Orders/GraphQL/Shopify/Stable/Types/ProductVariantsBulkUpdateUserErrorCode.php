<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class ProductVariantsBulkUpdateUserErrorCode
{
    public const APP_NOT_AUTHORIZED = 'APP_NOT_AUTHORIZED';
    public const BLANK = 'BLANK';
    public const CANNOT_SET_NAME_FOR_LINKED_OPTION_VALUE = 'CANNOT_SET_NAME_FOR_LINKED_OPTION_VALUE';
    public const CAPABILITY_VIOLATION = 'CAPABILITY_VIOLATION';
    public const DISALLOWED_OWNER_TYPE = 'DISALLOWED_OWNER_TYPE';
    public const GREATER_THAN_OR_EQUAL_TO = 'GREATER_THAN_OR_EQUAL_TO';
    public const INCLUSION = 'INCLUSION';
    public const INVALID_TYPE = 'INVALID_TYPE';
    public const INVALID_VALUE = 'INVALID_VALUE';
    public const NEED_TO_ADD_OPTION_VALUES = 'NEED_TO_ADD_OPTION_VALUES';
    public const NEGATIVE_PRICE_VALUE = 'NEGATIVE_PRICE_VALUE';
    public const NO_INVENTORY_QUANTITES_DURING_UPDATE = 'NO_INVENTORY_QUANTITES_DURING_UPDATE';
    public const NO_INVENTORY_QUANTITIES_ON_VARIANTS_UPDATE = 'NO_INVENTORY_QUANTITIES_ON_VARIANTS_UPDATE';
    public const OPTION_VALUES_FOR_NUMBER_OF_UNKNOWN_OPTIONS = 'OPTION_VALUES_FOR_NUMBER_OF_UNKNOWN_OPTIONS';
    public const PRESENT = 'PRESENT';
    public const PRODUCT_DOES_NOT_EXIST = 'PRODUCT_DOES_NOT_EXIST';
    public const PRODUCT_SUSPENDED = 'PRODUCT_SUSPENDED';
    public const PRODUCT_VARIANT_DOES_NOT_EXIST = 'PRODUCT_VARIANT_DOES_NOT_EXIST';
    public const PRODUCT_VARIANT_ID_MISSING = 'PRODUCT_VARIANT_ID_MISSING';
    public const SUBSCRIPTION_VIOLATION = 'SUBSCRIPTION_VIOLATION';
    public const TAKEN = 'TAKEN';
    public const TOO_LONG = 'TOO_LONG';
    public const TOO_SHORT = 'TOO_SHORT';
    public const UNSTRUCTURED_RESERVED_NAMESPACE = 'UNSTRUCTURED_RESERVED_NAMESPACE';
    public const VARIANT_ALREADY_EXISTS = 'VARIANT_ALREADY_EXISTS';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
