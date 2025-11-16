<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class GiftCardErrorCode
{
    public const GREATER_THAN = 'GREATER_THAN';
    public const INTERNAL_ERROR = 'INTERNAL_ERROR';
    public const INVALID = 'INVALID';
    public const MAX_INITIAL_VALUE_EXCEEDED = 'MAX_INITIAL_VALUE_EXCEEDED';
    public const MAX_INITIAL_VALUE_EXCEEDED_IN_USD = 'MAX_INITIAL_VALUE_EXCEEDED_IN_USD';
    public const MISSING_ARGUMENT = 'MISSING_ARGUMENT';
    public const TAKEN = 'TAKEN';
    public const TOO_LONG = 'TOO_LONG';
    public const TOO_SHORT = 'TOO_SHORT';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
