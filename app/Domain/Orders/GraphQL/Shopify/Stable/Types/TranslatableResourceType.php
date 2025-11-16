<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class TranslatableResourceType
{
    public const COLLECTION = 'COLLECTION';
    public const COOKIE_BANNER = 'COOKIE_BANNER';
    public const DELIVERY_METHOD_DEFINITION = 'DELIVERY_METHOD_DEFINITION';
    public const EMAIL_TEMPLATE = 'EMAIL_TEMPLATE';
    public const FILTER = 'FILTER';
    public const LINK = 'LINK';
    public const METAFIELD = 'METAFIELD';
    public const METAOBJECT = 'METAOBJECT';
    public const ONLINE_STORE_ARTICLE = 'ONLINE_STORE_ARTICLE';
    public const ONLINE_STORE_BLOG = 'ONLINE_STORE_BLOG';
    public const ONLINE_STORE_MENU = 'ONLINE_STORE_MENU';
    public const ONLINE_STORE_PAGE = 'ONLINE_STORE_PAGE';
    public const ONLINE_STORE_THEME = 'ONLINE_STORE_THEME';
    public const ONLINE_STORE_THEME_APP_EMBED = 'ONLINE_STORE_THEME_APP_EMBED';
    public const ONLINE_STORE_THEME_JSON_TEMPLATE = 'ONLINE_STORE_THEME_JSON_TEMPLATE';
    public const ONLINE_STORE_THEME_LOCALE_CONTENT = 'ONLINE_STORE_THEME_LOCALE_CONTENT';
    public const ONLINE_STORE_THEME_SECTION_GROUP = 'ONLINE_STORE_THEME_SECTION_GROUP';
    public const ONLINE_STORE_THEME_SETTINGS_CATEGORY = 'ONLINE_STORE_THEME_SETTINGS_CATEGORY';
    public const ONLINE_STORE_THEME_SETTINGS_DATA_SECTIONS = 'ONLINE_STORE_THEME_SETTINGS_DATA_SECTIONS';
    public const PACKING_SLIP_TEMPLATE = 'PACKING_SLIP_TEMPLATE';
    public const PAYMENT_GATEWAY = 'PAYMENT_GATEWAY';
    public const PRODUCT = 'PRODUCT';
    public const PRODUCT_OPTION = 'PRODUCT_OPTION';
    public const PRODUCT_OPTION_VALUE = 'PRODUCT_OPTION_VALUE';
    public const SELLING_PLAN = 'SELLING_PLAN';
    public const SELLING_PLAN_GROUP = 'SELLING_PLAN_GROUP';
    public const SHOP = 'SHOP';
    public const SHOP_POLICY = 'SHOP_POLICY';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
