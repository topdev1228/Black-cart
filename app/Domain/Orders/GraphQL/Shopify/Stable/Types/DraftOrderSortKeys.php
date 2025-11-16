<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class DraftOrderSortKeys
{
    public const CUSTOMER_NAME = 'CUSTOMER_NAME';
    public const ID = 'ID';
    public const NUMBER = 'NUMBER';
    public const RELEVANCE = 'RELEVANCE';
    public const STATUS = 'STATUS';
    public const TOTAL_PRICE = 'TOTAL_PRICE';
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
