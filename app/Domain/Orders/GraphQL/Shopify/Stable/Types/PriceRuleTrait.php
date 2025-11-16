<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class PriceRuleTrait
{
    public const BULK = 'BULK';
    public const BUY_ONE_GET_ONE = 'BUY_ONE_GET_ONE';
    public const BUY_ONE_GET_ONE_WITH_ALLOCATION_LIMIT = 'BUY_ONE_GET_ONE_WITH_ALLOCATION_LIMIT';
    public const QUANTITY_DISCOUNTS = 'QUANTITY_DISCOUNTS';
    public const SPECIFIC_CUSTOMERS = 'SPECIFIC_CUSTOMERS';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
