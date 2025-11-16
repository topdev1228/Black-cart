<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class AppDeveloperType
{
    public const MERCHANT = 'MERCHANT';
    public const PARTNER = 'PARTNER';
    public const SHOPIFY = 'SHOPIFY';
    public const UNKNOWN = 'UNKNOWN';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
