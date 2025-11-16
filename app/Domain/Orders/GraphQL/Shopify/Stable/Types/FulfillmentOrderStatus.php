<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class FulfillmentOrderStatus
{
    public const CANCELLED = 'CANCELLED';
    public const CLOSED = 'CLOSED';
    public const INCOMPLETE = 'INCOMPLETE';
    public const IN_PROGRESS = 'IN_PROGRESS';
    public const ON_HOLD = 'ON_HOLD';
    public const OPEN = 'OPEN';
    public const SCHEDULED = 'SCHEDULED';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
