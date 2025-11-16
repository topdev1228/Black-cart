<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class FulfillmentStatus
{
    public const CANCELLED = 'CANCELLED';
    public const ERROR = 'ERROR';
    public const FAILURE = 'FAILURE';
    public const OPEN = 'OPEN';
    public const PENDING = 'PENDING';
    public const SUCCESS = 'SUCCESS';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
