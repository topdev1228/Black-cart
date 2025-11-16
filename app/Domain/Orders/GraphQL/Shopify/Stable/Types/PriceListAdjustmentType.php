<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class PriceListAdjustmentType
{
    public const PERCENTAGE_DECREASE = 'PERCENTAGE_DECREASE';
    public const PERCENTAGE_INCREASE = 'PERCENTAGE_INCREASE';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
