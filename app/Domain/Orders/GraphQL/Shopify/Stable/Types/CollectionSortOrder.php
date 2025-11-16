<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class CollectionSortOrder
{
    public const ALPHA_ASC = 'ALPHA_ASC';
    public const ALPHA_DESC = 'ALPHA_DESC';
    public const BEST_SELLING = 'BEST_SELLING';
    public const CREATED = 'CREATED';
    public const CREATED_DESC = 'CREATED_DESC';
    public const MANUAL = 'MANUAL';
    public const PRICE_ASC = 'PRICE_ASC';
    public const PRICE_DESC = 'PRICE_DESC';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
