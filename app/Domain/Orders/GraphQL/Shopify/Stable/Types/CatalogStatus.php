<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class CatalogStatus
{
    public const ACTIVE = 'ACTIVE';
    public const ARCHIVED = 'ARCHIVED';
    public const DRAFT = 'DRAFT';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
