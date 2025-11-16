<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class BulkOperationErrorCode
{
    public const ACCESS_DENIED = 'ACCESS_DENIED';
    public const INTERNAL_SERVER_ERROR = 'INTERNAL_SERVER_ERROR';
    public const TIMEOUT = 'TIMEOUT';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
