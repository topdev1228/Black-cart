<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class CustomerMergeErrorCode
{
    public const CUSTOMER_HAS_GIFT_CARDS = 'CUSTOMER_HAS_GIFT_CARDS';
    public const INTERNAL_ERROR = 'INTERNAL_ERROR';
    public const INVALID_CUSTOMER = 'INVALID_CUSTOMER';
    public const INVALID_CUSTOMER_ID = 'INVALID_CUSTOMER_ID';
    public const MISSING_OVERRIDE_ATTRIBUTE = 'MISSING_OVERRIDE_ATTRIBUTE';
    public const OVERRIDE_ATTRIBUTE_INVALID = 'OVERRIDE_ATTRIBUTE_INVALID';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
