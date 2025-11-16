<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class CartTransformDeleteUserErrorCode
{
    public const NOT_FOUND = 'NOT_FOUND';
    public const UNAUTHORIZED_APP_SCOPE = 'UNAUTHORIZED_APP_SCOPE';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
