<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class CheckoutBrandingTypographySize
{
    public const BASE = 'BASE';
    public const EXTRA_EXTRA_LARGE = 'EXTRA_EXTRA_LARGE';
    public const EXTRA_LARGE = 'EXTRA_LARGE';
    public const EXTRA_SMALL = 'EXTRA_SMALL';
    public const LARGE = 'LARGE';
    public const MEDIUM = 'MEDIUM';
    public const SMALL = 'SMALL';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
