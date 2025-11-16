<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class AppSubscriptionTrialExtendUserErrorCode
{
    public const SUBSCRIPTION_NOT_ACTIVE = 'SUBSCRIPTION_NOT_ACTIVE';
    public const SUBSCRIPTION_NOT_FOUND = 'SUBSCRIPTION_NOT_FOUND';
    public const TRIAL_NOT_ACTIVE = 'TRIAL_NOT_ACTIVE';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
