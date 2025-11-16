<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class FileCreateInputDuplicateResolutionMode
{
    public const APPEND_UUID = 'APPEND_UUID';
    public const RAISE_ERROR = 'RAISE_ERROR';
    public const REPLACE = 'REPLACE';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
