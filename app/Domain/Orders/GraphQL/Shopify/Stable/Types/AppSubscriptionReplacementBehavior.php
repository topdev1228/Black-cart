<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class AppSubscriptionReplacementBehavior
{
    public const APPLY_IMMEDIATELY = 'APPLY_IMMEDIATELY';
    public const APPLY_ON_NEXT_BILLING_CYCLE = 'APPLY_ON_NEXT_BILLING_CYCLE';
    public const STANDARD = 'STANDARD';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
