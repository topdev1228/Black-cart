<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class OrderInvoiceSendUserErrorCode
{
    public const ORDER_INVOICE_SEND_UNSUCCESSFUL = 'ORDER_INVOICE_SEND_UNSUCCESSFUL';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
