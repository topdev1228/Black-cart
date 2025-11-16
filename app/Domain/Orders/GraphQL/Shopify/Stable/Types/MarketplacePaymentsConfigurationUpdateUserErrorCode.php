<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class MarketplacePaymentsConfigurationUpdateUserErrorCode
{
    public const EMPTY_FEATURES = 'EMPTY_FEATURES';
    public const INVALID_FEATURE = 'INVALID_FEATURE';
    public const NON_ONBOARDABLE_REQUIRED_FEATURE = 'NON_ONBOARDABLE_REQUIRED_FEATURE';
    public const NOT_SAVED = 'NOT_SAVED';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
