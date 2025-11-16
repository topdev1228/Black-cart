<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class CheckoutBrandingCornerRadius
{
    public const BASE = 'BASE';
    public const LARGE = 'LARGE';
    public const NONE = 'NONE';
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
