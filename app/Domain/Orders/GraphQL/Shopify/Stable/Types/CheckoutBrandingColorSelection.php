<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class CheckoutBrandingColorSelection
{
    public const COLOR1 = 'COLOR1';
    public const COLOR2 = 'COLOR2';
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
