<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class CheckoutBrandingBorderWidth
{
    public const BASE = 'BASE';
    public const LARGE_100 = 'LARGE_100';
    public const LARGE_200 = 'LARGE_200';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
