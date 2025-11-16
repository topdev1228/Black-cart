<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class PerformanceAggregationLevel
{
    public const DAILY = 'DAILY';
    public const MONTHLY = 'MONTHLY';
    public const ROLLING28DAYS = 'ROLLING28DAYS';
    public const WEEKLY = 'WEEKLY';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
