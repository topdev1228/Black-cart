<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class CustomerMergeRequestStatus
{
    public const COMPLETED = 'COMPLETED';
    public const FAILED = 'FAILED';
    public const IN_PROGRESS = 'IN_PROGRESS';
    public const REQUESTED = 'REQUESTED';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
