<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class OrderCreateMandatePaymentUserErrorCode
{
    public const ORDER_MANDATE_PAYMENT_ERROR_CODE = 'ORDER_MANDATE_PAYMENT_ERROR_CODE';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
