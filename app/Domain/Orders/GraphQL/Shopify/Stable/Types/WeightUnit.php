<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class WeightUnit
{
    public const GRAMS = 'GRAMS';
    public const KILOGRAMS = 'KILOGRAMS';
    public const OUNCES = 'OUNCES';
    public const POUNDS = 'POUNDS';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
