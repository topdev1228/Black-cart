<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class MetafieldDefinitionDeleteUserErrorCode
{
    public const DISALLOWED_OWNER_TYPE = 'DISALLOWED_OWNER_TYPE';
    public const INTERNAL_ERROR = 'INTERNAL_ERROR';
    public const METAFIELD_DEFINITION_IN_USE = 'METAFIELD_DEFINITION_IN_USE';
    public const NOT_FOUND = 'NOT_FOUND';
    public const PRESENT = 'PRESENT';
    public const REFERENCE_TYPE_DELETION_ERROR = 'REFERENCE_TYPE_DELETION_ERROR';
    public const RESERVED_NAMESPACE_ORPHANED_METAFIELDS = 'RESERVED_NAMESPACE_ORPHANED_METAFIELDS';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
