<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class SellingPlanInterval
{
    public const DAY = 'DAY';
    public const MONTH = 'MONTH';
    public const WEEK = 'WEEK';
    public const YEAR = 'YEAR';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
