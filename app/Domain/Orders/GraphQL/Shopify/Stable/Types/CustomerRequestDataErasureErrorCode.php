<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class CustomerRequestDataErasureErrorCode
{
    public const DOES_NOT_EXIST = 'DOES_NOT_EXIST';
    public const FAILED_TO_REQUEST = 'FAILED_TO_REQUEST';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
