<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class ShopifyPaymentsBalanceTransactionPayoutStatus
{
    public const ACTION_REQUIRED = 'ACTION_REQUIRED';
    public const CANCELED = 'CANCELED';
    public const FAILED = 'FAILED';
    public const IN_TRANSIT = 'IN_TRANSIT';
    public const PAID = 'PAID';
    public const PENDING = 'PENDING';
    public const SCHEDULED = 'SCHEDULED';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
