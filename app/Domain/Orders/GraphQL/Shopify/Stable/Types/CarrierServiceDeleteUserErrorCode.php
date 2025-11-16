<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class CarrierServiceDeleteUserErrorCode
{
    public const CARRIER_SERVICE_DELETE_FAILED = 'CARRIER_SERVICE_DELETE_FAILED';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
