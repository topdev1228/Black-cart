<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class OrderSortKeys
{
    public const CREATED_AT = 'CREATED_AT';
    public const CUSTOMER_NAME = 'CUSTOMER_NAME';
    public const DESTINATION = 'DESTINATION';
    public const FINANCIAL_STATUS = 'FINANCIAL_STATUS';
    public const FULFILLMENT_STATUS = 'FULFILLMENT_STATUS';
    public const ID = 'ID';
    public const ORDER_NUMBER = 'ORDER_NUMBER';
    public const PO_NUMBER = 'PO_NUMBER';
    public const PROCESSED_AT = 'PROCESSED_AT';
    public const RELEVANCE = 'RELEVANCE';
    public const TOTAL_ITEMS_QUANTITY = 'TOTAL_ITEMS_QUANTITY';
    public const TOTAL_PRICE = 'TOTAL_PRICE';
    public const UPDATED_AT = 'UPDATED_AT';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
