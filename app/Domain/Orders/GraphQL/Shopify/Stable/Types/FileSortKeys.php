<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class FileSortKeys
{
    public const CREATED_AT = 'CREATED_AT';
    public const FILENAME = 'FILENAME';
    public const ID = 'ID';
    public const ORIGINAL_UPLOAD_SIZE = 'ORIGINAL_UPLOAD_SIZE';
    public const RELEVANCE = 'RELEVANCE';
    public const UPDATED_AT = 'UPDATED_AT';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
