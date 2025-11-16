<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class CustomerAccountsVersion
{
    public const CLASSIC = 'CLASSIC';
    public const NEW_CUSTOMER_ACCOUNTS = 'NEW_CUSTOMER_ACCOUNTS';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
