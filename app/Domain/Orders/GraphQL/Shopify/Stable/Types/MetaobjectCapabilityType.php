<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class MetaobjectCapabilityType
{
    public const ONLINE_STORE = 'ONLINE_STORE';
    public const PUBLISHABLE = 'PUBLISHABLE';
    public const RENDERABLE = 'RENDERABLE';
    public const TRANSLATABLE = 'TRANSLATABLE';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
