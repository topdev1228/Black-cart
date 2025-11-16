<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class CustomerCancelDataErasureErrorCode
{
    public const DOES_NOT_EXIST = 'DOES_NOT_EXIST';
    public const FAILED_TO_CANCEL = 'FAILED_TO_CANCEL';
    public const NOT_BEING_ERASED = 'NOT_BEING_ERASED';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
