<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class UrlRedirectImportErrorCode
{
    public const ALREADY_IMPORTED = 'ALREADY_IMPORTED';
    public const FILE_DOES_NOT_EXIST = 'FILE_DOES_NOT_EXIST';
    public const IN_PROGRESS = 'IN_PROGRESS';
    public const NOT_FOUND = 'NOT_FOUND';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
