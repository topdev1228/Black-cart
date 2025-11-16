<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class CheckoutBrandingHeaderPosition
{
    public const INLINE = 'INLINE';
    public const INLINE_SECONDARY = 'INLINE_SECONDARY';
    public const START = 'START';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
