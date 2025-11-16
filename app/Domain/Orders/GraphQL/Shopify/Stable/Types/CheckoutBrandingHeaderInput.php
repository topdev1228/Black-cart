<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string|null $alignment
 * @property string|null $background
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingImageInput|null $banner
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingHeaderCartLinkInput|null $cartLink
 * @property string|null $colorScheme
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingLogoInput|null $logo
 * @property string|null $padding
 * @property string|null $position
 */
class CheckoutBrandingHeaderInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string|null $alignment
     * @param string|null $background
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingImageInput|null $banner
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingHeaderCartLinkInput|null $cartLink
     * @param string|null $colorScheme
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingLogoInput|null $logo
     * @param string|null $padding
     * @param string|null $position
     */
    public static function make(
        $alignment = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $background = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $banner = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $cartLink = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $colorScheme = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $logo = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $padding = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $position = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($alignment !== self::UNDEFINED) {
            $instance->alignment = $alignment;
        }
        if ($background !== self::UNDEFINED) {
            $instance->background = $background;
        }
        if ($banner !== self::UNDEFINED) {
            $instance->banner = $banner;
        }
        if ($cartLink !== self::UNDEFINED) {
            $instance->cartLink = $cartLink;
        }
        if ($colorScheme !== self::UNDEFINED) {
            $instance->colorScheme = $colorScheme;
        }
        if ($logo !== self::UNDEFINED) {
            $instance->logo = $logo;
        }
        if ($padding !== self::UNDEFINED) {
            $instance->padding = $padding;
        }
        if ($position !== self::UNDEFINED) {
            $instance->position = $position;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'alignment' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'background' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'banner' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingImageInput),
            'cartLink' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingHeaderCartLinkInput),
            'colorScheme' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'logo' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingLogoInput),
            'padding' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'position' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
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
