<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class HydrogenStorefrontCustomerUserErrorCode
{
    public const CUSTOMER_ACCOUNT_API_APPLICATION_NOT_FOUND = 'CUSTOMER_ACCOUNT_API_APPLICATION_NOT_FOUND';
    public const INVALID = 'INVALID';
    public const REGEX_INVALID = 'REGEX_INVALID';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
