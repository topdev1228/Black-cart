<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class ShopifyPaymentsDisputeReason
{
    public const BANK_CANNOT_PROCESS = 'BANK_CANNOT_PROCESS';
    public const CREDIT_NOT_PROCESSED = 'CREDIT_NOT_PROCESSED';
    public const CUSTOMER_INITIATED = 'CUSTOMER_INITIATED';
    public const DEBIT_NOT_AUTHORIZED = 'DEBIT_NOT_AUTHORIZED';
    public const DUPLICATE = 'DUPLICATE';
    public const FRAUDULENT = 'FRAUDULENT';
    public const GENERAL = 'GENERAL';
    public const INCORRECT_ACCOUNT_DETAILS = 'INCORRECT_ACCOUNT_DETAILS';
    public const INSUFFICIENT_FUNDS = 'INSUFFICIENT_FUNDS';
    public const PRODUCT_NOT_RECEIVED = 'PRODUCT_NOT_RECEIVED';
    public const PRODUCT_UNACCEPTABLE = 'PRODUCT_UNACCEPTABLE';
    public const SUBSCRIPTION_CANCELLED = 'SUBSCRIPTION_CANCELLED';
    public const UNRECOGNIZED = 'UNRECOGNIZED';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
