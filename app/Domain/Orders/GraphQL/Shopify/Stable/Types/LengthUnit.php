<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class LengthUnit
{
    public const CENTIMETERS = 'CENTIMETERS';
    public const FEET = 'FEET';
    public const INCHES = 'INCHES';
    public const METERS = 'METERS';
    public const MILLIMETERS = 'MILLIMETERS';
    public const YARDS = 'YARDS';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
