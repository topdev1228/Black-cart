<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class DayOfTheWeek
{
    public const FRIDAY = 'FRIDAY';
    public const MONDAY = 'MONDAY';
    public const SATURDAY = 'SATURDAY';
    public const SUNDAY = 'SUNDAY';
    public const THURSDAY = 'THURSDAY';
    public const TUESDAY = 'TUESDAY';
    public const WEDNESDAY = 'WEDNESDAY';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
