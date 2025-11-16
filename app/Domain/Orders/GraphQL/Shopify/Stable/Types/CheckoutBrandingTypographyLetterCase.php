<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class CheckoutBrandingTypographyLetterCase
{
    public const LOWER = 'LOWER';
    public const NONE = 'NONE';
    public const TITLE = 'TITLE';
    public const UPPER = 'UPPER';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
