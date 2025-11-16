<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class BillingAttemptUserErrorCode
{
    public const BLANK = 'BLANK';
    public const CONTRACT_NOT_FOUND = 'CONTRACT_NOT_FOUND';
    public const CONTRACT_TERMINATED = 'CONTRACT_TERMINATED';
    public const CONTRACT_UNDER_REVIEW = 'CONTRACT_UNDER_REVIEW';
    public const CYCLE_INDEX_OUT_OF_RANGE = 'CYCLE_INDEX_OUT_OF_RANGE';
    public const CYCLE_START_DATE_OUT_OF_RANGE = 'CYCLE_START_DATE_OUT_OF_RANGE';
    public const INVALID = 'INVALID';
    public const ORIGIN_TIME_BEFORE_CONTRACT_CREATION = 'ORIGIN_TIME_BEFORE_CONTRACT_CREATION';
    public const ORIGIN_TIME_OUT_OF_RANGE = 'ORIGIN_TIME_OUT_OF_RANGE';
    public const UPCOMING_CYCLE_LIMIT_EXCEEDED = 'UPCOMING_CYCLE_LIMIT_EXCEEDED';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
