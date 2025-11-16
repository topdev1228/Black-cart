<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class MetafieldDefinitionUpdateUserErrorCode
{
    public const DISALLOWED_OWNER_TYPE = 'DISALLOWED_OWNER_TYPE';
    public const GRANT_LIMIT_EXCEEDED = 'GRANT_LIMIT_EXCEEDED';
    public const INTERNAL_ERROR = 'INTERNAL_ERROR';
    public const INVALID_INPUT = 'INVALID_INPUT';
    public const INVALID_INPUT_COMBINATION = 'INVALID_INPUT_COMBINATION';
    public const METAFIELD_DEFINITION_IN_USE = 'METAFIELD_DEFINITION_IN_USE';
    public const METAOBJECT_DEFINITION_CHANGED = 'METAOBJECT_DEFINITION_CHANGED';
    public const NOT_FOUND = 'NOT_FOUND';
    public const OWNER_TYPE_LIMIT_EXCEEDED_FOR_AUTOMATED_COLLECTIONS = 'OWNER_TYPE_LIMIT_EXCEEDED_FOR_AUTOMATED_COLLECTIONS';
    public const PINNED_LIMIT_REACHED = 'PINNED_LIMIT_REACHED';
    public const PRESENT = 'PRESENT';
    public const TOO_LONG = 'TOO_LONG';
    public const TYPE_NOT_ALLOWED_FOR_CONDITIONS = 'TYPE_NOT_ALLOWED_FOR_CONDITIONS';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
