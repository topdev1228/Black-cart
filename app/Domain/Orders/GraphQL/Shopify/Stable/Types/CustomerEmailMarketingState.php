<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class CustomerEmailMarketingState
{
    public const INVALID = 'INVALID';
    public const NOT_SUBSCRIBED = 'NOT_SUBSCRIBED';
    public const PENDING = 'PENDING';
    public const REDACTED = 'REDACTED';
    public const SUBSCRIBED = 'SUBSCRIBED';
    public const UNSUBSCRIBED = 'UNSUBSCRIBED';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
