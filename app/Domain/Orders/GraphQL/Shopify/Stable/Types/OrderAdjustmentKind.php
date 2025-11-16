<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class OrderAdjustmentKind
{
    public const REFUND_DISCREPANCY = 'REFUND_DISCREPANCY';
    public const SHIPPING_REFUND = 'SHIPPING_REFUND';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
