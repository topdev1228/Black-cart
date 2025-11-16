<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class UrlRedirectBulkDeleteBySavedSearchUserErrorCode
{
    public const INVALID_SAVED_SEARCH_QUERY = 'INVALID_SAVED_SEARCH_QUERY';
    public const SAVED_SEARCH_NOT_FOUND = 'SAVED_SEARCH_NOT_FOUND';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
