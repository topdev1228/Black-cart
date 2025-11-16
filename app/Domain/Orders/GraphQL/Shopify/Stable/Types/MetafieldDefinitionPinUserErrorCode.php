<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class MetafieldDefinitionPinUserErrorCode
{
    public const ALREADY_PINNED = 'ALREADY_PINNED';
    public const DISALLOWED_OWNER_TYPE = 'DISALLOWED_OWNER_TYPE';
    public const INTERNAL_ERROR = 'INTERNAL_ERROR';
    public const NOT_FOUND = 'NOT_FOUND';
    public const PINNED_LIMIT_REACHED = 'PINNED_LIMIT_REACHED';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
