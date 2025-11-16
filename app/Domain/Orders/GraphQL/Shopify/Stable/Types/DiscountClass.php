<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class DiscountClass
{
    public const ORDER = 'ORDER';
    public const PRODUCT = 'PRODUCT';
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
