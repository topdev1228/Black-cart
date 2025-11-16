<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class SubscriptionBillingAttemptErrorCode
{
    public const AMOUNT_TOO_SMALL = 'AMOUNT_TOO_SMALL';
    public const AUTHENTICATION_ERROR = 'AUTHENTICATION_ERROR';
    public const BUYER_CANCELED_PAYMENT_METHOD = 'BUYER_CANCELED_PAYMENT_METHOD';
    public const CARD_NUMBER_INCORRECT = 'CARD_NUMBER_INCORRECT';
    public const CUSTOMER_INVALID = 'CUSTOMER_INVALID';
    public const CUSTOMER_NOT_FOUND = 'CUSTOMER_NOT_FOUND';
    public const EXPIRED_PAYMENT_METHOD = 'EXPIRED_PAYMENT_METHOD';
    public const FRAUD_SUSPECTED = 'FRAUD_SUSPECTED';
    public const INSUFFICIENT_FUNDS = 'INSUFFICIENT_FUNDS';
    public const INVALID_CUSTOMER_BILLING_AGREEMENT = 'INVALID_CUSTOMER_BILLING_AGREEMENT';
    public const INVALID_PAYMENT_METHOD = 'INVALID_PAYMENT_METHOD';
    public const INVALID_SHIPPING_ADDRESS = 'INVALID_SHIPPING_ADDRESS';
    public const INVENTORY_ALLOCATIONS_NOT_FOUND = 'INVENTORY_ALLOCATIONS_NOT_FOUND';
    public const INVOICE_ALREADY_PAID = 'INVOICE_ALREADY_PAID';
    public const PAYMENT_METHOD_DECLINED = 'PAYMENT_METHOD_DECLINED';
    public const PAYMENT_METHOD_INCOMPATIBLE_WITH_GATEWAY_CONFIG = 'PAYMENT_METHOD_INCOMPATIBLE_WITH_GATEWAY_CONFIG';
    public const PAYMENT_METHOD_NOT_FOUND = 'PAYMENT_METHOD_NOT_FOUND';
    public const PAYMENT_PROVIDER_IS_NOT_ENABLED = 'PAYMENT_PROVIDER_IS_NOT_ENABLED';
    public const PAYPAL_ERROR_GENERAL = 'PAYPAL_ERROR_GENERAL';
    public const PURCHASE_TYPE_NOT_SUPPORTED = 'PURCHASE_TYPE_NOT_SUPPORTED';
    public const TEST_MODE = 'TEST_MODE';
    public const TRANSIENT_ERROR = 'TRANSIENT_ERROR';
    public const UNEXPECTED_ERROR = 'UNEXPECTED_ERROR';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
