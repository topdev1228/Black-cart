<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class SubscriptionDiscountRejectionReason
{
    public const CURRENTLY_INACTIVE = 'CURRENTLY_INACTIVE';
    public const CUSTOMER_NOT_ELIGIBLE = 'CUSTOMER_NOT_ELIGIBLE';
    public const CUSTOMER_USAGE_LIMIT_REACHED = 'CUSTOMER_USAGE_LIMIT_REACHED';
    public const INCOMPATIBLE_PURCHASE_TYPE = 'INCOMPATIBLE_PURCHASE_TYPE';
    public const INTERNAL_ERROR = 'INTERNAL_ERROR';
    public const NOT_FOUND = 'NOT_FOUND';
    public const NO_ENTITLED_LINE_ITEMS = 'NO_ENTITLED_LINE_ITEMS';
    public const NO_ENTITLED_SHIPPING_LINES = 'NO_ENTITLED_SHIPPING_LINES';
    public const PURCHASE_NOT_IN_RANGE = 'PURCHASE_NOT_IN_RANGE';
    public const QUANTITY_NOT_IN_RANGE = 'QUANTITY_NOT_IN_RANGE';
    public const USAGE_LIMIT_REACHED = 'USAGE_LIMIT_REACHED';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
