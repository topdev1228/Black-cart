<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class CollectionSortKeys
{
    public const ID = 'ID';
    public const RELEVANCE = 'RELEVANCE';
    public const SORT_ORDER = 'SORT_ORDER';
    public const TITLE = 'TITLE';
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
