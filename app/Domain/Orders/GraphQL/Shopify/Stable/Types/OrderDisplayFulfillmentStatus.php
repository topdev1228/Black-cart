<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class OrderDisplayFulfillmentStatus
{
    public const FULFILLED = 'FULFILLED';
    public const IN_PROGRESS = 'IN_PROGRESS';
    public const ON_HOLD = 'ON_HOLD';
    public const OPEN = 'OPEN';
    public const PARTIALLY_FULFILLED = 'PARTIALLY_FULFILLED';
    public const PENDING_FULFILLMENT = 'PENDING_FULFILLMENT';
    public const RESTOCKED = 'RESTOCKED';
    public const SCHEDULED = 'SCHEDULED';
    public const UNFULFILLED = 'UNFULFILLED';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
