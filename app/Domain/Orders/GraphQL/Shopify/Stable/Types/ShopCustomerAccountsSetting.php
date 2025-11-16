<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class ShopCustomerAccountsSetting
{
    public const DISABLED = 'DISABLED';
    public const OPTIONAL = 'OPTIONAL';
    public const REQUIRED = 'REQUIRED';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
