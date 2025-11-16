<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class MetafieldDefinitionCreateUserErrorCode
{
    public const DISALLOWED_OWNER_TYPE = 'DISALLOWED_OWNER_TYPE';
    public const DUPLICATE_OPTION = 'DUPLICATE_OPTION';
    public const GRANT_LIMIT_EXCEEDED = 'GRANT_LIMIT_EXCEEDED';
    public const INCLUSION = 'INCLUSION';
    public const INVALID = 'INVALID';
    public const INVALID_CHARACTER = 'INVALID_CHARACTER';
    public const INVALID_INPUT_COMBINATION = 'INVALID_INPUT_COMBINATION';
    public const INVALID_OPTION = 'INVALID_OPTION';
    public const LIMIT_EXCEEDED = 'LIMIT_EXCEEDED';
    public const OWNER_TYPE_LIMIT_EXCEEDED_FOR_AUTOMATED_COLLECTIONS = 'OWNER_TYPE_LIMIT_EXCEEDED_FOR_AUTOMATED_COLLECTIONS';
    public const PINNED_LIMIT_REACHED = 'PINNED_LIMIT_REACHED';
    public const PRESENT = 'PRESENT';
    public const RESERVED_NAMESPACE_KEY = 'RESERVED_NAMESPACE_KEY';
    public const RESOURCE_TYPE_LIMIT_EXCEEDED = 'RESOURCE_TYPE_LIMIT_EXCEEDED';
    public const TAKEN = 'TAKEN';
    public const TOO_LONG = 'TOO_LONG';
    public const TOO_SHORT = 'TOO_SHORT';
    public const TYPE_NOT_ALLOWED_FOR_CONDITIONS = 'TYPE_NOT_ALLOWED_FOR_CONDITIONS';
    public const UNSTRUCTURED_ALREADY_EXISTS = 'UNSTRUCTURED_ALREADY_EXISTS';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
