<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class ShopifyPaymentsPayoutTransactionType
{
    public const DEPOSIT = 'DEPOSIT';
    public const WITHDRAWAL = 'WITHDRAWAL';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
