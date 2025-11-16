<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class DiscountType
{
    public const AUTOMATIC_DISCOUNT = 'AUTOMATIC_DISCOUNT';
    public const CODE_DISCOUNT = 'CODE_DISCOUNT';
    public const MANUAL = 'MANUAL';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
