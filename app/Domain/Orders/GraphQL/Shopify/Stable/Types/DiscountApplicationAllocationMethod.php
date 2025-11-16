<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class DiscountApplicationAllocationMethod
{
    public const ACROSS = 'ACROSS';
    public const EACH = 'EACH';
    public const ONE = 'ONE';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
