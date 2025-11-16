<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class AbandonmentUpdateActivitiesDeliveryStatusesUserErrorCode
{
    public const ABANDONMENT_NOT_FOUND = 'ABANDONMENT_NOT_FOUND';
    public const DELIVERY_STATUS_INFO_NOT_FOUND = 'DELIVERY_STATUS_INFO_NOT_FOUND';
    public const MARKETING_ACTIVITY_NOT_FOUND = 'MARKETING_ACTIVITY_NOT_FOUND';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
