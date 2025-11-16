<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class OrderTransactionErrorCode
{
    public const AMAZON_PAYMENTS_INVALID_PAYMENT_METHOD = 'AMAZON_PAYMENTS_INVALID_PAYMENT_METHOD';
    public const AMAZON_PAYMENTS_MAX_AMOUNT_CHARGED = 'AMAZON_PAYMENTS_MAX_AMOUNT_CHARGED';
    public const AMAZON_PAYMENTS_MAX_AMOUNT_REFUNDED = 'AMAZON_PAYMENTS_MAX_AMOUNT_REFUNDED';
    public const AMAZON_PAYMENTS_MAX_AUTHORIZATIONS_CAPTURED = 'AMAZON_PAYMENTS_MAX_AUTHORIZATIONS_CAPTURED';
    public const AMAZON_PAYMENTS_MAX_REFUNDS_PROCESSED = 'AMAZON_PAYMENTS_MAX_REFUNDS_PROCESSED';
    public const AMAZON_PAYMENTS_ORDER_REFERENCE_CANCELED = 'AMAZON_PAYMENTS_ORDER_REFERENCE_CANCELED';
    public const AMAZON_PAYMENTS_STALE = 'AMAZON_PAYMENTS_STALE';
    public const CALL_ISSUER = 'CALL_ISSUER';
    public const CARD_DECLINED = 'CARD_DECLINED';
    public const CONFIG_ERROR = 'CONFIG_ERROR';
    public const EXPIRED_CARD = 'EXPIRED_CARD';
    public const GENERIC_ERROR = 'GENERIC_ERROR';
    public const INCORRECT_ADDRESS = 'INCORRECT_ADDRESS';
    public const INCORRECT_CVC = 'INCORRECT_CVC';
    public const INCORRECT_NUMBER = 'INCORRECT_NUMBER';
    public const INCORRECT_PIN = 'INCORRECT_PIN';
    public const INCORRECT_ZIP = 'INCORRECT_ZIP';
    public const INVALID_AMOUNT = 'INVALID_AMOUNT';
    public const INVALID_COUNTRY = 'INVALID_COUNTRY';
    public const INVALID_CVC = 'INVALID_CVC';
    public const INVALID_EXPIRY_DATE = 'INVALID_EXPIRY_DATE';
    public const INVALID_NUMBER = 'INVALID_NUMBER';
    public const PAYMENT_METHOD_UNAVAILABLE = 'PAYMENT_METHOD_UNAVAILABLE';
    public const PICK_UP_CARD = 'PICK_UP_CARD';
    public const PROCESSING_ERROR = 'PROCESSING_ERROR';
    public const TEST_MODE_LIVE_CARD = 'TEST_MODE_LIVE_CARD';
    public const UNSUPPORTED_FEATURE = 'UNSUPPORTED_FEATURE';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
