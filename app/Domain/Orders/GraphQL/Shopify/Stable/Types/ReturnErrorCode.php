<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class ReturnErrorCode
{
    public const ALREADY_EXISTS = 'ALREADY_EXISTS';
    public const BLANK = 'BLANK';
    public const CREATION_FAILED = 'CREATION_FAILED';
    public const EQUAL_TO = 'EQUAL_TO';
    public const FEATURE_NOT_ENABLED = 'FEATURE_NOT_ENABLED';
    public const GREATER_THAN = 'GREATER_THAN';
    public const GREATER_THAN_OR_EQUAL_TO = 'GREATER_THAN_OR_EQUAL_TO';
    public const INCLUSION = 'INCLUSION';
    public const INTERNAL_ERROR = 'INTERNAL_ERROR';
    public const INVALID = 'INVALID';
    public const INVALID_STATE = 'INVALID_STATE';
    public const LESS_THAN = 'LESS_THAN';
    public const LESS_THAN_OR_EQUAL_TO = 'LESS_THAN_OR_EQUAL_TO';
    public const NOTIFICATION_FAILED = 'NOTIFICATION_FAILED';
    public const NOT_A_NUMBER = 'NOT_A_NUMBER';
    public const NOT_EDITABLE = 'NOT_EDITABLE';
    public const NOT_FOUND = 'NOT_FOUND';
    public const PRESENT = 'PRESENT';
    public const TAKEN = 'TAKEN';
    public const TOO_BIG = 'TOO_BIG';
    public const TOO_LONG = 'TOO_LONG';
    public const TOO_MANY_ARGUMENTS = 'TOO_MANY_ARGUMENTS';
    public const TOO_SHORT = 'TOO_SHORT';
    public const WRONG_LENGTH = 'WRONG_LENGTH';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
