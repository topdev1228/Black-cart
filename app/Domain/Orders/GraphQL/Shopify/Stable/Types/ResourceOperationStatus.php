<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class ResourceOperationStatus
{
    public const ACTIVE = 'ACTIVE';
    public const COMPLETE = 'COMPLETE';
    public const CREATED = 'CREATED';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
