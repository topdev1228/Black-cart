<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class PriceRuleTarget
{
    public const LINE_ITEM = 'LINE_ITEM';
    public const SHIPPING_LINE = 'SHIPPING_LINE';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
