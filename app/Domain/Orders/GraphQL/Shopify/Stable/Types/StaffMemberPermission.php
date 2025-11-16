<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class StaffMemberPermission
{
    public const APPLICATIONS = 'APPLICATIONS';
    public const CHANNELS = 'CHANNELS';
    public const CUSTOMERS = 'CUSTOMERS';
    public const DASHBOARD = 'DASHBOARD';
    public const DOMAINS = 'DOMAINS';
    public const DRAFT_ORDERS = 'DRAFT_ORDERS';
    public const EDIT_ORDERS = 'EDIT_ORDERS';
    public const EDIT_THEME_CODE = 'EDIT_THEME_CODE';
    public const FULL = 'FULL';
    public const GIFT_CARDS = 'GIFT_CARDS';
    public const LINKS = 'LINKS';
    public const LOCATIONS = 'LOCATIONS';
    public const MANAGE_DELIVERY_SETTINGS = 'MANAGE_DELIVERY_SETTINGS';
    public const MANAGE_POLICIES = 'MANAGE_POLICIES';
    public const MANAGE_TAXES_SETTINGS = 'MANAGE_TAXES_SETTINGS';
    public const MARKETING = 'MARKETING';
    public const MARKETING_SECTION = 'MARKETING_SECTION';
    public const ORDERS = 'ORDERS';
    public const OVERVIEWS = 'OVERVIEWS';
    public const PAGES = 'PAGES';
    public const PAY_DRAFT_ORDERS_BY_CREDIT_CARD = 'PAY_DRAFT_ORDERS_BY_CREDIT_CARD';
    public const PAY_ORDERS_BY_CREDIT_CARD = 'PAY_ORDERS_BY_CREDIT_CARD';
    public const PAY_ORDERS_BY_VAULTED_CARD = 'PAY_ORDERS_BY_VAULTED_CARD';
    public const PREFERENCES = 'PREFERENCES';
    public const PRODUCTS = 'PRODUCTS';
    public const REFUND_ORDERS = 'REFUND_ORDERS';
    public const REPORTS = 'REPORTS';
    public const THEMES = 'THEMES';
    public const TRANSLATIONS = 'TRANSLATIONS';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
