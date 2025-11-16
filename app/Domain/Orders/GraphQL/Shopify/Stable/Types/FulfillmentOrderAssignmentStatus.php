<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class FulfillmentOrderAssignmentStatus
{
    public const CANCELLATION_REQUESTED = 'CANCELLATION_REQUESTED';
    public const FULFILLMENT_ACCEPTED = 'FULFILLMENT_ACCEPTED';
    public const FULFILLMENT_REQUESTED = 'FULFILLMENT_REQUESTED';
    public const FULFILLMENT_UNSUBMITTED = 'FULFILLMENT_UNSUBMITTED';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
