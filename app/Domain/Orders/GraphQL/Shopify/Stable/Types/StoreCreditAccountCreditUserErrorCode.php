<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class StoreCreditAccountCreditUserErrorCode
{
    public const ACCOUNT_NOT_FOUND = 'ACCOUNT_NOT_FOUND';
    public const CREDIT_LIMIT_EXCEEDED = 'CREDIT_LIMIT_EXCEEDED';
    public const EXPIRES_AT_IN_PAST = 'EXPIRES_AT_IN_PAST';
    public const MISMATCHING_CURRENCY = 'MISMATCHING_CURRENCY';
    public const NEGATIVE_OR_ZERO_AMOUNT = 'NEGATIVE_OR_ZERO_AMOUNT';
    public const OWNER_NOT_FOUND = 'OWNER_NOT_FOUND';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
