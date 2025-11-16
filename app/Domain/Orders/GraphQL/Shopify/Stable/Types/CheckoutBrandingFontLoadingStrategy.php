<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class CheckoutBrandingFontLoadingStrategy
{
    public const AUTO = 'AUTO';
    public const BLOCK = 'BLOCK';
    public const FALLBACK = 'FALLBACK';
    public const OPTIONAL = 'OPTIONAL';
    public const SWAP = 'SWAP';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
