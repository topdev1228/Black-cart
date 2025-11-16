<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class ShopifyProtectStatus
{
    public const ACTIVE = 'ACTIVE';
    public const INACTIVE = 'INACTIVE';
    public const NOT_PROTECTED = 'NOT_PROTECTED';
    public const PENDING = 'PENDING';
    public const PROTECTED = 'PROTECTED';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
