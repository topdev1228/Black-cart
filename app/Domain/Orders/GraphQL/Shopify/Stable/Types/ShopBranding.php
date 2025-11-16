<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class ShopBranding
{
    public const ROGERS = 'ROGERS';
    public const SHOPIFY = 'SHOPIFY';
    public const SHOPIFY_GOLD = 'SHOPIFY_GOLD';
    public const SHOPIFY_PLUS = 'SHOPIFY_PLUS';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
