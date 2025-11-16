<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class AppSubscriptionStatus
{
    public const ACCEPTED = 'ACCEPTED';
    public const ACTIVE = 'ACTIVE';
    public const CANCELLED = 'CANCELLED';
    public const DECLINED = 'DECLINED';
    public const EXPIRED = 'EXPIRED';
    public const FROZEN = 'FROZEN';
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
