<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class GiftCardSortKeys
{
    public const AMOUNT_SPENT = 'AMOUNT_SPENT';
    public const BALANCE = 'BALANCE';
    public const CODE = 'CODE';
    public const CREATED_AT = 'CREATED_AT';
    public const CUSTOMER_NAME = 'CUSTOMER_NAME';
    public const DISABLED_AT = 'DISABLED_AT';
    public const EXPIRES_ON = 'EXPIRES_ON';
    public const ID = 'ID';
    public const INITIAL_VALUE = 'INITIAL_VALUE';
    public const RELEVANCE = 'RELEVANCE';
    public const UPDATED_AT = 'UPDATED_AT';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
