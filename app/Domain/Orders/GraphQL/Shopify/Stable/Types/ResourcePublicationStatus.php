<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class ResourcePublicationStatus
{
    public const APPROVED = 'APPROVED';
    public const NOT_APPROVED = 'NOT_APPROVED';
    public const PENDING = 'PENDING';
    public const UNSET = 'UNSET';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
