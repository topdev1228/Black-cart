<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class ShopifyPaymentsSourceType
{
    public const ADJUSTMENT = 'ADJUSTMENT';
    public const ADJUSTMENT_REVERSAL = 'ADJUSTMENT_REVERSAL';
    public const CHARGE = 'CHARGE';
    public const DISPUTE = 'DISPUTE';
    public const REFUND = 'REFUND';
    public const SYSTEM_ADJUSTMENT = 'SYSTEM_ADJUSTMENT';
    public const TRANSFER = 'TRANSFER';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
