<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class CustomerPaymentMethodRemoteUserErrorCode
{
    public const AUTHORIZE_NET_NOT_ENABLED_FOR_SUBSCRIPTIONS = 'AUTHORIZE_NET_NOT_ENABLED_FOR_SUBSCRIPTIONS';
    public const BLANK = 'BLANK';
    public const BRAINTREE_NOT_ENABLED_FOR_SUBSCRIPTIONS = 'BRAINTREE_NOT_ENABLED_FOR_SUBSCRIPTIONS';
    public const EXACTLY_ONE_REMOTE_REFERENCE_REQUIRED = 'EXACTLY_ONE_REMOTE_REFERENCE_REQUIRED';
    public const INVALID = 'INVALID';
    public const PRESENT = 'PRESENT';
    public const TAKEN = 'TAKEN';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
