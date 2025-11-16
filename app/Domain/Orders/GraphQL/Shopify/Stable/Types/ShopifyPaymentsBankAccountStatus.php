<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class ShopifyPaymentsBankAccountStatus
{
    public const ERRORED = 'ERRORED';
    public const NEW = 'NEW';
    public const VALIDATED = 'VALIDATED';
    public const VERIFIED = 'VERIFIED';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
