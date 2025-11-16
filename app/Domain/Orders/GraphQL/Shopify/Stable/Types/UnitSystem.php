<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class UnitSystem
{
    public const IMPERIAL_SYSTEM = 'IMPERIAL_SYSTEM';
    public const METRIC_SYSTEM = 'METRIC_SYSTEM';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
