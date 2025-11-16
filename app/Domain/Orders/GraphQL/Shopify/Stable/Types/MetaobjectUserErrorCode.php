<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class MetaobjectUserErrorCode
{
    public const BLANK = 'BLANK';
    public const CAPABILITY_NOT_ENABLED = 'CAPABILITY_NOT_ENABLED';
    public const DUPLICATE_FIELD_INPUT = 'DUPLICATE_FIELD_INPUT';
    public const FIELD_TYPE_INVALID = 'FIELD_TYPE_INVALID';
    public const IMMUTABLE = 'IMMUTABLE';
    public const INCLUSION = 'INCLUSION';
    public const INTERNAL_ERROR = 'INTERNAL_ERROR';
    public const INVALID = 'INVALID';
    public const INVALID_OPTION = 'INVALID_OPTION';
    public const INVALID_TYPE = 'INVALID_TYPE';
    public const INVALID_VALUE = 'INVALID_VALUE';
    public const MAX_DEFINITIONS_EXCEEDED = 'MAX_DEFINITIONS_EXCEEDED';
    public const MAX_OBJECTS_EXCEEDED = 'MAX_OBJECTS_EXCEEDED';
    public const MISSING_REQUIRED_KEYS = 'MISSING_REQUIRED_KEYS';
    public const NOT_AUTHORIZED = 'NOT_AUTHORIZED';
    public const OBJECT_FIELD_REQUIRED = 'OBJECT_FIELD_REQUIRED';
    public const OBJECT_FIELD_TAKEN = 'OBJECT_FIELD_TAKEN';
    public const PRESENT = 'PRESENT';
    public const RECORD_NOT_FOUND = 'RECORD_NOT_FOUND';
    public const RESERVED_NAME = 'RESERVED_NAME';
    public const TAKEN = 'TAKEN';
    public const TOO_LONG = 'TOO_LONG';
    public const TOO_SHORT = 'TOO_SHORT';
    public const UNDEFINED_OBJECT_FIELD = 'UNDEFINED_OBJECT_FIELD';
    public const UNDEFINED_OBJECT_TYPE = 'UNDEFINED_OBJECT_TYPE';
    public const URL_HANDLE_BLANK = 'URL_HANDLE_BLANK';
    public const URL_HANDLE_INVALID = 'URL_HANDLE_INVALID';
    public const URL_HANDLE_TAKEN = 'URL_HANDLE_TAKEN';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
