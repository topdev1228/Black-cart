<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class BalanceTransactionSortKeys
{
    public const AMOUNT = 'AMOUNT';
    public const FEE = 'FEE';
    public const ID = 'ID';
    public const NET = 'NET';
    public const ORDER_NAME = 'ORDER_NAME';
    public const PAYMENT_METHOD_NAME = 'PAYMENT_METHOD_NAME';
    public const PAYOUT_DATE = 'PAYOUT_DATE';
    public const PAYOUT_STATUS = 'PAYOUT_STATUS';
    public const PROCESSED_AT = 'PROCESSED_AT';
    public const RELEVANCE = 'RELEVANCE';
    public const TRANSACTION_TYPE = 'TRANSACTION_TYPE';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
