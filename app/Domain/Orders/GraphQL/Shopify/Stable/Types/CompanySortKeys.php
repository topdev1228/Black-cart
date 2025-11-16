<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class CompanySortKeys
{
    public const CREATED_AT = 'CREATED_AT';
    public const ID = 'ID';
    public const NAME = 'NAME';
    public const ORDER_COUNT = 'ORDER_COUNT';
    public const RELEVANCE = 'RELEVANCE';
    public const SINCE_DATE = 'SINCE_DATE';
    public const TOTAL_SPENT = 'TOTAL_SPENT';
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
