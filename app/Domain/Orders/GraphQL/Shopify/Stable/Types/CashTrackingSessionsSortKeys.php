<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class CashTrackingSessionsSortKeys
{
    public const CLOSING_TIME_ASC = 'CLOSING_TIME_ASC';
    public const CLOSING_TIME_DESC = 'CLOSING_TIME_DESC';
    public const ID = 'ID';
    public const OPENING_TIME_ASC = 'OPENING_TIME_ASC';
    public const OPENING_TIME_DESC = 'OPENING_TIME_DESC';
    public const RELEVANCE = 'RELEVANCE';
    public const TOTAL_DISCREPANCY_ASC = 'TOTAL_DISCREPANCY_ASC';
    public const TOTAL_DISCREPANCY_DESC = 'TOTAL_DISCREPANCY_DESC';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
