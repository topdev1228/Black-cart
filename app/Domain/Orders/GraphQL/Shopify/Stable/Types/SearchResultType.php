<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class SearchResultType
{
    public const BALANCE_TRANSACTION = 'BALANCE_TRANSACTION';
    public const COLLECTION = 'COLLECTION';
    public const CUSTOMER = 'CUSTOMER';
    public const DISCOUNT_REDEEM_CODE = 'DISCOUNT_REDEEM_CODE';
    public const DRAFT_ORDER = 'DRAFT_ORDER';
    public const FILE = 'FILE';
    public const ONLINE_STORE_ARTICLE = 'ONLINE_STORE_ARTICLE';
    public const ONLINE_STORE_BLOG = 'ONLINE_STORE_BLOG';
    public const ONLINE_STORE_PAGE = 'ONLINE_STORE_PAGE';
    public const ORDER = 'ORDER';
    public const PRICE_RULE = 'PRICE_RULE';
    public const PRODUCT = 'PRODUCT';
    public const URL_REDIRECT = 'URL_REDIRECT';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
