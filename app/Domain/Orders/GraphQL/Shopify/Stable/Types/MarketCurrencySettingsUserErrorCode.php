<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class MarketCurrencySettingsUserErrorCode
{
    public const MANAGED_MARKET = 'MANAGED_MARKET';
    public const MARKET_NOT_FOUND = 'MARKET_NOT_FOUND';
    public const MULTIPLE_CURRENCIES_NOT_SUPPORTED = 'MULTIPLE_CURRENCIES_NOT_SUPPORTED';
    public const NO_LOCAL_CURRENCIES_ON_SINGLE_COUNTRY_MARKET = 'NO_LOCAL_CURRENCIES_ON_SINGLE_COUNTRY_MARKET';
    public const PRIMARY_MARKET_USES_SHOP_CURRENCY = 'PRIMARY_MARKET_USES_SHOP_CURRENCY';
    public const UNSUPPORTED_CURRENCY = 'UNSUPPORTED_CURRENCY';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
