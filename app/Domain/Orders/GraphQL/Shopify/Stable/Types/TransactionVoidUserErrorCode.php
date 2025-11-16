<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class TransactionVoidUserErrorCode
{
    public const AUTH_NOT_SUCCESSFUL = 'AUTH_NOT_SUCCESSFUL';
    public const AUTH_NOT_VOIDABLE = 'AUTH_NOT_VOIDABLE';
    public const GENERIC_ERROR = 'GENERIC_ERROR';
    public const TRANSACTION_NOT_FOUND = 'TRANSACTION_NOT_FOUND';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
