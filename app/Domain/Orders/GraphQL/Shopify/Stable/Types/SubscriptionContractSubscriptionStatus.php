<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class SubscriptionContractSubscriptionStatus
{
    public const ACTIVE = 'ACTIVE';
    public const CANCELLED = 'CANCELLED';
    public const EXPIRED = 'EXPIRED';
    public const FAILED = 'FAILED';
    public const PAUSED = 'PAUSED';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
