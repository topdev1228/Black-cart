<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class DeliveryMethodType
{
    public const LOCAL = 'LOCAL';
    public const NONE = 'NONE';
    public const PICKUP_POINT = 'PICKUP_POINT';
    public const PICK_UP = 'PICK_UP';
    public const RETAIL = 'RETAIL';
    public const SHIPPING = 'SHIPPING';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
