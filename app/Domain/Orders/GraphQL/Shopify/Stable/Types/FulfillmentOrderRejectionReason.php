<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class FulfillmentOrderRejectionReason
{
    public const INCORRECT_ADDRESS = 'INCORRECT_ADDRESS';
    public const INELIGIBLE_PRODUCT = 'INELIGIBLE_PRODUCT';
    public const INVENTORY_OUT_OF_STOCK = 'INVENTORY_OUT_OF_STOCK';
    public const OTHER = 'OTHER';
    public const UNDELIVERABLE_DESTINATION = 'UNDELIVERABLE_DESTINATION';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
