<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class CustomerPaymentMethodGetDuplicationDataUserErrorCode
{
    public const CUSTOMER_DOES_NOT_EXIST = 'CUSTOMER_DOES_NOT_EXIST';
    public const INVALID_INSTRUMENT = 'INVALID_INSTRUMENT';
    public const INVALID_ORGANIZATION_SHOP = 'INVALID_ORGANIZATION_SHOP';
    public const PAYMENT_METHOD_DOES_NOT_EXIST = 'PAYMENT_METHOD_DOES_NOT_EXIST';
    public const SAME_SHOP = 'SAME_SHOP';
    public const TOO_MANY_REQUESTS = 'TOO_MANY_REQUESTS';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
