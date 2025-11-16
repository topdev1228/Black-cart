<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class DeliveryLocationLocalPickupSettingsErrorCode
{
    public const ACTIVE_LOCATION_NOT_FOUND = 'ACTIVE_LOCATION_NOT_FOUND';
    public const GENERIC_ERROR = 'GENERIC_ERROR';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
