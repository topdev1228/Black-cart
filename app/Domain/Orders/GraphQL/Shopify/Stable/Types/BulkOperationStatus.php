<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class BulkOperationStatus
{
    public const CANCELED = 'CANCELED';
    public const CANCELING = 'CANCELING';
    public const COMPLETED = 'COMPLETED';
    public const CREATED = 'CREATED';
    public const EXPIRED = 'EXPIRED';
    public const FAILED = 'FAILED';
    public const RUNNING = 'RUNNING';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
