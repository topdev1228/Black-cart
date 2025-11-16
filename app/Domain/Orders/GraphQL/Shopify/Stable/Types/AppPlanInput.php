<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\AppRecurringPricingInput|null $appRecurringPricingDetails
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\AppUsagePricingInput|null $appUsagePricingDetails
 */
class AppPlanInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\AppRecurringPricingInput|null $appRecurringPricingDetails
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\AppUsagePricingInput|null $appUsagePricingDetails
     */
    public static function make(
        $appRecurringPricingDetails = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $appUsagePricingDetails = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($appRecurringPricingDetails !== self::UNDEFINED) {
            $instance->appRecurringPricingDetails = $appRecurringPricingDetails;
        }
        if ($appUsagePricingDetails !== self::UNDEFINED) {
            $instance->appUsagePricingDetails = $appUsagePricingDetails;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'appRecurringPricingDetails' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\AppRecurringPricingInput),
            'appUsagePricingDetails' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\AppUsagePricingInput),
        ];
    }

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
