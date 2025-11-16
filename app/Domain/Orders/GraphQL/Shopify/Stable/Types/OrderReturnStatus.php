<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class OrderReturnStatus
{
    public const INSPECTION_COMPLETE = 'INSPECTION_COMPLETE';
    public const IN_PROGRESS = 'IN_PROGRESS';
    public const NO_RETURN = 'NO_RETURN';
    public const RETURNED = 'RETURNED';
    public const RETURN_FAILED = 'RETURN_FAILED';
    public const RETURN_REQUESTED = 'RETURN_REQUESTED';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
