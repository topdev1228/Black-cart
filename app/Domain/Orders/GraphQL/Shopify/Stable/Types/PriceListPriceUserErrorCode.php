<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class PriceListPriceUserErrorCode
{
    public const BLANK = 'BLANK';
    public const PRICE_LIST_CURRENCY_MISMATCH = 'PRICE_LIST_CURRENCY_MISMATCH';
    public const PRICE_LIST_NOT_FOUND = 'PRICE_LIST_NOT_FOUND';
    public const PRICE_NOT_FIXED = 'PRICE_NOT_FIXED';
    public const VARIANT_NOT_FOUND = 'VARIANT_NOT_FOUND';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
