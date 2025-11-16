<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class OrderActionType
{
    public const ORDER = 'ORDER';
    public const ORDER_EDIT = 'ORDER_EDIT';
    public const REFUND = 'REFUND';
    public const RETURN = 'RETURN';
    public const UNKNOWN = 'UNKNOWN';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
