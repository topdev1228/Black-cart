<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class UrlRedirectBulkDeleteBySearchUserErrorCode
{
    public const INVALID_SEARCH_ARGUMENT = 'INVALID_SEARCH_ARGUMENT';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
