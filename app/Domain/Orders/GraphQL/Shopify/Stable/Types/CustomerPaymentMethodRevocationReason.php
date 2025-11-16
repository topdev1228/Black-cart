<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class CustomerPaymentMethodRevocationReason
{
    public const AUTHORIZE_NET_GATEWAY_NOT_ENABLED = 'AUTHORIZE_NET_GATEWAY_NOT_ENABLED';
    public const AUTHORIZE_NET_RETURNED_NO_PAYMENT_METHOD = 'AUTHORIZE_NET_RETURNED_NO_PAYMENT_METHOD';
    public const BRAINTREE_API_AUTHENTICATION_ERROR = 'BRAINTREE_API_AUTHENTICATION_ERROR';
    public const BRAINTREE_GATEWAY_NOT_ENABLED = 'BRAINTREE_GATEWAY_NOT_ENABLED';
    public const BRAINTREE_PAYMENT_METHOD_NOT_CARD = 'BRAINTREE_PAYMENT_METHOD_NOT_CARD';
    public const BRAINTREE_RETURNED_NO_PAYMENT_METHOD = 'BRAINTREE_RETURNED_NO_PAYMENT_METHOD';
    public const FAILED_TO_UPDATE_CREDIT_CARD = 'FAILED_TO_UPDATE_CREDIT_CARD';
    public const MANUALLY_REVOKED = 'MANUALLY_REVOKED';
    public const MERGED = 'MERGED';
    public const STRIPE_API_AUTHENTICATION_ERROR = 'STRIPE_API_AUTHENTICATION_ERROR';
    public const STRIPE_API_INVALID_REQUEST_ERROR = 'STRIPE_API_INVALID_REQUEST_ERROR';
    public const STRIPE_GATEWAY_NOT_ENABLED = 'STRIPE_GATEWAY_NOT_ENABLED';
    public const STRIPE_PAYMENT_METHOD_NOT_CARD = 'STRIPE_PAYMENT_METHOD_NOT_CARD';
    public const STRIPE_RETURNED_NO_PAYMENT_METHOD = 'STRIPE_RETURNED_NO_PAYMENT_METHOD';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
