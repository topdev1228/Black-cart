<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class MarketingTactic
{
    public const ABANDONED_CART = 'ABANDONED_CART';
    public const AD = 'AD';
    public const AFFILIATE = 'AFFILIATE';
    public const LINK = 'LINK';
    public const LOYALTY = 'LOYALTY';
    public const MESSAGE = 'MESSAGE';
    public const NEWSLETTER = 'NEWSLETTER';
    public const NOTIFICATION = 'NOTIFICATION';
    public const POST = 'POST';
    public const RETARGETING = 'RETARGETING';
    public const STOREFRONT_APP = 'STOREFRONT_APP';
    public const TRANSACTIONAL = 'TRANSACTIONAL';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
