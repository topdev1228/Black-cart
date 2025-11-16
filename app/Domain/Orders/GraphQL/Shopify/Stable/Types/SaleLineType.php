<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class SaleLineType
{
    public const ADDITIONAL_FEE = 'ADDITIONAL_FEE';
    public const ADJUSTMENT = 'ADJUSTMENT';
    public const DUTY = 'DUTY';
    public const FEE = 'FEE';
    public const GIFT_CARD = 'GIFT_CARD';
    public const PRODUCT = 'PRODUCT';
    public const SHIPPING = 'SHIPPING';
    public const TIP = 'TIP';
    public const UNKNOWN = 'UNKNOWN';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
