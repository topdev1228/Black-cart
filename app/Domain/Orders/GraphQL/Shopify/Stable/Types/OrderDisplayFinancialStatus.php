<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class OrderDisplayFinancialStatus
{
    public const AUTHORIZED = 'AUTHORIZED';
    public const EXPIRED = 'EXPIRED';
    public const PAID = 'PAID';
    public const PARTIALLY_PAID = 'PARTIALLY_PAID';
    public const PARTIALLY_REFUNDED = 'PARTIALLY_REFUNDED';
    public const PENDING = 'PENDING';
    public const REFUNDED = 'REFUNDED';
    public const VOIDED = 'VOIDED';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
