<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class SegmentSortKeys
{
    public const CREATION_DATE = 'CREATION_DATE';
    public const ID = 'ID';
    public const LAST_EDIT_DATE = 'LAST_EDIT_DATE';
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
