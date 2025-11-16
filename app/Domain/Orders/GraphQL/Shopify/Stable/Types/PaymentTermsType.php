<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class PaymentTermsType
{
    public const FIXED = 'FIXED';
    public const FULFILLMENT = 'FULFILLMENT';
    public const NET = 'NET';
    public const RECEIPT = 'RECEIPT';
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
