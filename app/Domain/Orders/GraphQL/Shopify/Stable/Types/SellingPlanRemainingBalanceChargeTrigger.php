<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class SellingPlanRemainingBalanceChargeTrigger
{
    public const EXACT_TIME = 'EXACT_TIME';
    public const NO_REMAINING_BALANCE = 'NO_REMAINING_BALANCE';
    public const TIME_AFTER_CHECKOUT = 'TIME_AFTER_CHECKOUT';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
