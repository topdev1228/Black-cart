<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class FulfillmentServiceType
{
    public const GIFT_CARD = 'GIFT_CARD';
    public const MANUAL = 'MANUAL';
    public const THIRD_PARTY = 'THIRD_PARTY';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
