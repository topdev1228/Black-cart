<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingFontGroupInput|null $primary
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingFontGroupInput|null $secondary
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingFontSizeInput|null $size
 */
class CheckoutBrandingTypographyInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingFontGroupInput|null $primary
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingFontGroupInput|null $secondary
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingFontSizeInput|null $size
     */
    public static function make(
        $primary = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $secondary = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $size = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($primary !== self::UNDEFINED) {
            $instance->primary = $primary;
        }
        if ($secondary !== self::UNDEFINED) {
            $instance->secondary = $secondary;
        }
        if ($size !== self::UNDEFINED) {
            $instance->size = $size;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'primary' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingFontGroupInput),
            'secondary' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingFontGroupInput),
            'size' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingFontSizeInput),
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
