<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingColorGroupInput|null $canvas
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingColorGroupInput|null $color1
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingColorGroupInput|null $color2
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingColorGroupInput|null $critical
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingColorGroupInput|null $interactive
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingColorGroupInput|null $primary
 */
class CheckoutBrandingColorPaletteInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingColorGroupInput|null $canvas
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingColorGroupInput|null $color1
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingColorGroupInput|null $color2
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingColorGroupInput|null $critical
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingColorGroupInput|null $interactive
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingColorGroupInput|null $primary
     */
    public static function make(
        $canvas = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $color1 = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $color2 = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $critical = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $interactive = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $primary = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($canvas !== self::UNDEFINED) {
            $instance->canvas = $canvas;
        }
        if ($color1 !== self::UNDEFINED) {
            $instance->color1 = $color1;
        }
        if ($color2 !== self::UNDEFINED) {
            $instance->color2 = $color2;
        }
        if ($critical !== self::UNDEFINED) {
            $instance->critical = $critical;
        }
        if ($interactive !== self::UNDEFINED) {
            $instance->interactive = $interactive;
        }
        if ($primary !== self::UNDEFINED) {
            $instance->primary = $primary;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'canvas' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingColorGroupInput),
            'color1' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingColorGroupInput),
            'color2' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingColorGroupInput),
            'critical' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingColorGroupInput),
            'interactive' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingColorGroupInput),
            'primary' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingColorGroupInput),
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
