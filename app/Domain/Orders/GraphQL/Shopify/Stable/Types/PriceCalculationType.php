<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class PriceCalculationType
{
    public const COMPONENTS_SUM = 'COMPONENTS_SUM';
    public const FIXED = 'FIXED';
    public const NONE = 'NONE';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
