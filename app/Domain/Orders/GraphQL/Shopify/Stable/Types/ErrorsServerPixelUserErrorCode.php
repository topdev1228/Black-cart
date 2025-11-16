<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class ErrorsServerPixelUserErrorCode
{
    public const ALREADY_EXISTS = 'ALREADY_EXISTS';
    public const NEEDS_CONFIGURATION_TO_CONNECT = 'NEEDS_CONFIGURATION_TO_CONNECT';
    public const NOT_FOUND = 'NOT_FOUND';
    public const PUB_SUB_ERROR = 'PUB_SUB_ERROR';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
