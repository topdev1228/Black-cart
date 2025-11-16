<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class MetafieldOwnerType
{
    public const API_PERMISSION = 'API_PERMISSION';
    public const ARTICLE = 'ARTICLE';
    public const BLOG = 'BLOG';
    public const BRAND = 'BRAND';
    public const CARTTRANSFORM = 'CARTTRANSFORM';
    public const COLLECTION = 'COLLECTION';
    public const COMPANY = 'COMPANY';
    public const COMPANY_LOCATION = 'COMPANY_LOCATION';
    public const CUSTOMER = 'CUSTOMER';
    public const DELIVERY_CUSTOMIZATION = 'DELIVERY_CUSTOMIZATION';
    public const DELIVERY_METHOD = 'DELIVERY_METHOD';
    public const DISCOUNT = 'DISCOUNT';
    public const DRAFTORDER = 'DRAFTORDER';
    public const FULFILLMENT_CONSTRAINT_RULE = 'FULFILLMENT_CONSTRAINT_RULE';
    public const GATE_CONFIGURATION = 'GATE_CONFIGURATION';
    public const LOCATION = 'LOCATION';
    public const MARKET = 'MARKET';
    public const MEDIA_IMAGE = 'MEDIA_IMAGE';
    public const ORDER = 'ORDER';
    public const ORDER_ROUTING_LOCATION_RULE = 'ORDER_ROUTING_LOCATION_RULE';
    public const PAGE = 'PAGE';
    public const PAYMENT_CUSTOMIZATION = 'PAYMENT_CUSTOMIZATION';
    public const PRODUCT = 'PRODUCT';
    public const PRODUCTIMAGE = 'PRODUCTIMAGE';
    public const PRODUCTVARIANT = 'PRODUCTVARIANT';
    public const SHOP = 'SHOP';
    public const VALIDATION = 'VALIDATION';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
