<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class ReturnStatus
{
    public const CANCELED = 'CANCELED';
    public const CLOSED = 'CLOSED';
    public const DECLINED = 'DECLINED';
    public const OPEN = 'OPEN';
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
