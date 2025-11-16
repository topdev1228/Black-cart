<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class DiscountsAllocatorFunctionUserErrorCode
{
    public const FUNCTION_NOT_FOUND = 'FUNCTION_NOT_FOUND';
    public const INELIGIBLE_SHOP = 'INELIGIBLE_SHOP';
    public const INTERNAL_ERROR = 'INTERNAL_ERROR';
    public const INVALID_FUNCTION_TYPE = 'INVALID_FUNCTION_TYPE';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
