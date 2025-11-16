<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class ValidationUserErrorCode
{
    public const APP_NOT_AUTHORIZED = 'APP_NOT_AUTHORIZED';
    public const BLANK = 'BLANK';
    public const CAPABILITY_VIOLATION = 'CAPABILITY_VIOLATION';
    public const CUSTOM_APP_FUNCTION_NOT_ELIGIBLE = 'CUSTOM_APP_FUNCTION_NOT_ELIGIBLE';
    public const DISALLOWED_OWNER_TYPE = 'DISALLOWED_OWNER_TYPE';
    public const FUNCTION_DOES_NOT_IMPLEMENT = 'FUNCTION_DOES_NOT_IMPLEMENT';
    public const FUNCTION_NOT_FOUND = 'FUNCTION_NOT_FOUND';
    public const FUNCTION_PENDING_DELETION = 'FUNCTION_PENDING_DELETION';
    public const INCLUSION = 'INCLUSION';
    public const INVALID_TYPE = 'INVALID_TYPE';
    public const INVALID_VALUE = 'INVALID_VALUE';
    public const NOT_FOUND = 'NOT_FOUND';
    public const PRESENT = 'PRESENT';
    public const PUBLIC_APP_NOT_ALLOWED = 'PUBLIC_APP_NOT_ALLOWED';
    public const TAKEN = 'TAKEN';
    public const TOO_LONG = 'TOO_LONG';
    public const TOO_SHORT = 'TOO_SHORT';
    public const UNSTRUCTURED_RESERVED_NAMESPACE = 'UNSTRUCTURED_RESERVED_NAMESPACE';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
