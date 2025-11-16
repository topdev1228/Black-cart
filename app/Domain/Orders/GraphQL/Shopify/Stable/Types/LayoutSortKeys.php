<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class LayoutSortKeys
{
    public const ID = 'ID';
    public const LOCATION_TOTAL_COUNT = 'LOCATION_TOTAL_COUNT';
    public const RELEVANCE = 'RELEVANCE';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
