<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class ShopifyProtectEligibilityStatus
{
    public const ELIGIBLE = 'ELIGIBLE';
    public const NOT_ELIGIBLE = 'NOT_ELIGIBLE';
    public const PENDING = 'PENDING';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
