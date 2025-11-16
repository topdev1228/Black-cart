<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class MarketingActivityStatus
{
    public const ACTIVE = 'ACTIVE';
    public const DELETED = 'DELETED';
    public const DELETED_EXTERNALLY = 'DELETED_EXTERNALLY';
    public const DISCONNECTED = 'DISCONNECTED';
    public const DRAFT = 'DRAFT';
    public const FAILED = 'FAILED';
    public const INACTIVE = 'INACTIVE';
    public const PAUSED = 'PAUSED';
    public const PENDING = 'PENDING';
    public const SCHEDULED = 'SCHEDULED';
    public const UNDEFINED = 'UNDEFINED';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
