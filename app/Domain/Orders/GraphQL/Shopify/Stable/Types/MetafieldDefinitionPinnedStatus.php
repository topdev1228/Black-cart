<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class MetafieldDefinitionPinnedStatus
{
    public const ANY = 'ANY';
    public const PINNED = 'PINNED';
    public const UNPINNED = 'UNPINNED';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
