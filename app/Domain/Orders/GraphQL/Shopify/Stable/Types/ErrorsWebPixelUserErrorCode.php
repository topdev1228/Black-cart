<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class ErrorsWebPixelUserErrorCode
{
    public const BLANK = 'BLANK';
    public const INVALID_SETTINGS = 'INVALID_SETTINGS';
    public const NOT_FOUND = 'NOT_FOUND';
    public const TAKEN = 'TAKEN';
    public const UNABLE_TO_DELETE = 'UNABLE_TO_DELETE';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
