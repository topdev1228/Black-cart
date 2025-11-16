<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class OrderTransactionKind
{
    public const AUTHORIZATION = 'AUTHORIZATION';
    public const CAPTURE = 'CAPTURE';
    public const CHANGE = 'CHANGE';
    public const EMV_AUTHORIZATION = 'EMV_AUTHORIZATION';
    public const REFUND = 'REFUND';
    public const SALE = 'SALE';
    public const SUGGESTED_REFUND = 'SUGGESTED_REFUND';
    public const VOID = 'VOID';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
