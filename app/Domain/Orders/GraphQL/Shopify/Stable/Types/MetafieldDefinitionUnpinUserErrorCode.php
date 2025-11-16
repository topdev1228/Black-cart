<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class MetafieldDefinitionUnpinUserErrorCode
{
    public const DISALLOWED_OWNER_TYPE = 'DISALLOWED_OWNER_TYPE';
    public const INTERNAL_ERROR = 'INTERNAL_ERROR';
    public const NOT_FOUND = 'NOT_FOUND';
    public const NOT_PINNED = 'NOT_PINNED';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
