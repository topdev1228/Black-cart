<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class CartTransformCreateUserErrorCode
{
    public const FUNCTION_ALREADY_REGISTERED = 'FUNCTION_ALREADY_REGISTERED';
    public const FUNCTION_DOES_NOT_IMPLEMENT = 'FUNCTION_DOES_NOT_IMPLEMENT';
    public const FUNCTION_NOT_FOUND = 'FUNCTION_NOT_FOUND';
    public const INPUT_INVALID = 'INPUT_INVALID';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
