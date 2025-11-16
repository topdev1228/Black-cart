<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class CheckoutBrandingColorSchemeSelection
{
    public const COLOR_SCHEME1 = 'COLOR_SCHEME1';
    public const COLOR_SCHEME2 = 'COLOR_SCHEME2';
    public const COLOR_SCHEME3 = 'COLOR_SCHEME3';
    public const COLOR_SCHEME4 = 'COLOR_SCHEME4';
    public const TRANSPARENT = 'TRANSPARENT';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
