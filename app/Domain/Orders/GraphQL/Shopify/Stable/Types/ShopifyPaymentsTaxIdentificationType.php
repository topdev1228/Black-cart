<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class ShopifyPaymentsTaxIdentificationType
{
    public const EIN = 'EIN';
    public const FULL_SSN = 'FULL_SSN';
    public const SSN_LAST4_DIGITS = 'SSN_LAST4_DIGITS';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
