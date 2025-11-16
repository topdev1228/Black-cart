<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class CashTrackingSessionTransactionsSortKeys
{
    public const ID = 'ID';
    public const PROCESSED_AT = 'PROCESSED_AT';
    public const RELEVANCE = 'RELEVANCE';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
