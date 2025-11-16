<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class SubscriptionBillingCycleScheduleEditInputScheduleEditReason
{
    public const BUYER_INITIATED = 'BUYER_INITIATED';
    public const DEV_INITIATED = 'DEV_INITIATED';
    public const MERCHANT_INITIATED = 'MERCHANT_INITIATED';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
