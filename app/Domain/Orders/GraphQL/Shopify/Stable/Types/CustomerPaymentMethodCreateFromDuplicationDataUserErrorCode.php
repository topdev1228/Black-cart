<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class CustomerPaymentMethodCreateFromDuplicationDataUserErrorCode
{
    public const CUSTOMER_DOES_NOT_EXIST = 'CUSTOMER_DOES_NOT_EXIST';
    public const INVALID_ENCRYPTED_DUPLICATION_DATA = 'INVALID_ENCRYPTED_DUPLICATION_DATA';
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
