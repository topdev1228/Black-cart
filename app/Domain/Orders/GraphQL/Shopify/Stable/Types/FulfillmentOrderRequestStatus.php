<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class FulfillmentOrderRequestStatus
{
    public const ACCEPTED = 'ACCEPTED';
    public const CANCELLATION_ACCEPTED = 'CANCELLATION_ACCEPTED';
    public const CANCELLATION_REJECTED = 'CANCELLATION_REJECTED';
    public const CANCELLATION_REQUESTED = 'CANCELLATION_REQUESTED';
    public const CLOSED = 'CLOSED';
    public const REJECTED = 'REJECTED';
    public const SUBMITTED = 'SUBMITTED';
    public const UNSUBMITTED = 'UNSUBMITTED';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
