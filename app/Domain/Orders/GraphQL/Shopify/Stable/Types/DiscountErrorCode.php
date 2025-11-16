<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class DiscountErrorCode
{
    public const ACTIVE_PERIOD_OVERLAP = 'ACTIVE_PERIOD_OVERLAP';
    public const BLANK = 'BLANK';
    public const CONFLICT = 'CONFLICT';
    public const DUPLICATE = 'DUPLICATE';
    public const EQUAL_TO = 'EQUAL_TO';
    public const EXCEEDED_MAX = 'EXCEEDED_MAX';
    public const GREATER_THAN = 'GREATER_THAN';
    public const GREATER_THAN_OR_EQUAL_TO = 'GREATER_THAN_OR_EQUAL_TO';
    public const IMPLICIT_DUPLICATE = 'IMPLICIT_DUPLICATE';
    public const INCLUSION = 'INCLUSION';
    public const INTERNAL_ERROR = 'INTERNAL_ERROR';
    public const INVALID = 'INVALID';
    public const INVALID_COMBINES_WITH_FOR_DISCOUNT_CLASS = 'INVALID_COMBINES_WITH_FOR_DISCOUNT_CLASS';
    public const INVALID_DISCOUNT_CLASS_FOR_PRICE_RULE = 'INVALID_DISCOUNT_CLASS_FOR_PRICE_RULE';
    public const LESS_THAN = 'LESS_THAN';
    public const LESS_THAN_OR_EQUAL_TO = 'LESS_THAN_OR_EQUAL_TO';
    public const MAX_APP_DISCOUNTS = 'MAX_APP_DISCOUNTS';
    public const MINIMUM_SUBTOTAL_AND_QUANTITY_RANGE_BOTH_PRESENT = 'MINIMUM_SUBTOTAL_AND_QUANTITY_RANGE_BOTH_PRESENT';
    public const MISSING_ARGUMENT = 'MISSING_ARGUMENT';
    public const PRESENT = 'PRESENT';
    public const TAKEN = 'TAKEN';
    public const TOO_LONG = 'TOO_LONG';
    public const TOO_MANY_ARGUMENTS = 'TOO_MANY_ARGUMENTS';
    public const TOO_SHORT = 'TOO_SHORT';
    public const VALUE_OUTSIDE_RANGE = 'VALUE_OUTSIDE_RANGE';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
