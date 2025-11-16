<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class ProductCollectionSortKeys
{
    public const BEST_SELLING = 'BEST_SELLING';
    public const COLLECTION_DEFAULT = 'COLLECTION_DEFAULT';
    public const CREATED = 'CREATED';
    public const ID = 'ID';
    public const MANUAL = 'MANUAL';
    public const PRICE = 'PRICE';
    public const RELEVANCE = 'RELEVANCE';
    public const TITLE = 'TITLE';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
