<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class CheckoutBrandingTypographyKerning
{
    public const BASE = 'BASE';
    public const EXTRA_LOOSE = 'EXTRA_LOOSE';
    public const LOOSE = 'LOOSE';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
