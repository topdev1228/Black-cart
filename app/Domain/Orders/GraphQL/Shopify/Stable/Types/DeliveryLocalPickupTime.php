<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class DeliveryLocalPickupTime
{
    public const FIVE_OR_MORE_DAYS = 'FIVE_OR_MORE_DAYS';
    public const FOUR_HOURS = 'FOUR_HOURS';
    public const ONE_HOUR = 'ONE_HOUR';
    public const TWENTY_FOUR_HOURS = 'TWENTY_FOUR_HOURS';
    public const TWO_HOURS = 'TWO_HOURS';
    public const TWO_TO_FOUR_DAYS = 'TWO_TO_FOUR_DAYS';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
