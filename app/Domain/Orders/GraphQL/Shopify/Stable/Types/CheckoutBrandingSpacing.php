<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class CheckoutBrandingSpacing
{
    public const BASE = 'BASE';
    public const EXTRA_LOOSE = 'EXTRA_LOOSE';
    public const EXTRA_TIGHT = 'EXTRA_TIGHT';
    public const LOOSE = 'LOOSE';
    public const NONE = 'NONE';
    public const TIGHT = 'TIGHT';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
