<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class SubscriptionBillingCycleErrorCode
{
    public const BILLING_DATE_SET_ON_SKIPPED = 'BILLING_DATE_SET_ON_SKIPPED';
    public const CYCLE_INDEX_OUT_OF_RANGE = 'CYCLE_INDEX_OUT_OF_RANGE';
    public const CYCLE_NOT_FOUND = 'CYCLE_NOT_FOUND';
    public const CYCLE_START_DATE_OUT_OF_RANGE = 'CYCLE_START_DATE_OUT_OF_RANGE';
    public const EMPTY_BILLING_CYCLE_EDIT_SCHEDULE_INPUT = 'EMPTY_BILLING_CYCLE_EDIT_SCHEDULE_INPUT';
    public const INCOMPLETE_BILLING_ATTEMPTS = 'INCOMPLETE_BILLING_ATTEMPTS';
    public const INVALID = 'INVALID';
    public const INVALID_CYCLE_INDEX = 'INVALID_CYCLE_INDEX';
    public const INVALID_DATE = 'INVALID_DATE';
    public const NO_CYCLE_EDITS = 'NO_CYCLE_EDITS';
    public const OUT_OF_BOUNDS = 'OUT_OF_BOUNDS';
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
