<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class RefundLineItemRestockType
{
    public const CANCEL = 'CANCEL';
    public const LEGACY_RESTOCK = 'LEGACY_RESTOCK';
    public const NO_RESTOCK = 'NO_RESTOCK';
    public const RETURN = 'RETURN';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
