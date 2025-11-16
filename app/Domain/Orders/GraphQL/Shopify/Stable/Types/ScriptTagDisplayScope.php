<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class ScriptTagDisplayScope
{
    public const ALL = 'ALL';
    public const ONLINE_STORE = 'ONLINE_STORE';
    public const ORDER_STATUS = 'ORDER_STATUS';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
