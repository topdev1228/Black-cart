<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceListAdjustmentInput $adjustment
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceListAdjustmentSettingsInput|null $settings
 */
class PriceListParentUpdateInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceListAdjustmentInput $adjustment
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceListAdjustmentSettingsInput|null $settings
     */
    public static function make(
        $adjustment,
        $settings = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($adjustment !== self::UNDEFINED) {
            $instance->adjustment = $adjustment;
        }
        if ($settings !== self::UNDEFINED) {
            $instance->settings = $settings;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'adjustment' => new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceListAdjustmentInput),
            'settings' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceListAdjustmentSettingsInput),
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
