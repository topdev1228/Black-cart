<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class StoreCreditAccountDebitUserErrorCode
{
    public const ACCOUNT_NOT_FOUND = 'ACCOUNT_NOT_FOUND';
    public const INSUFFICIENT_FUNDS = 'INSUFFICIENT_FUNDS';
    public const MISMATCHING_CURRENCY = 'MISMATCHING_CURRENCY';
    public const NEGATIVE_OR_ZERO_AMOUNT = 'NEGATIVE_OR_ZERO_AMOUNT';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
