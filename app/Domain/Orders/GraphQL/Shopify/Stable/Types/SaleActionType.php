<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class SaleActionType
{
    public const ORDER = 'ORDER';
    public const RETURN = 'RETURN';
    public const UNKNOWN = 'UNKNOWN';
    public const UPDATE = 'UPDATE';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
