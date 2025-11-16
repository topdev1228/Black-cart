<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class TransactionSupportedRefundType
{
    public const CARD_NOT_PRESENT_REFUND = 'CARD_NOT_PRESENT_REFUND';
    public const CARD_PRESENT_REFUND = 'CARD_PRESENT_REFUND';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
