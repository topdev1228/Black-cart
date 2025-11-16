<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingImageInput|null $backgroundImage
 * @property string|null $colorScheme
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingMainSectionInput|null $section
 */
class CheckoutBrandingMainInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingImageInput|null $backgroundImage
     * @param string|null $colorScheme
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingMainSectionInput|null $section
     */
    public static function make(
        $backgroundImage = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $colorScheme = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $section = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($backgroundImage !== self::UNDEFINED) {
            $instance->backgroundImage = $backgroundImage;
        }
        if ($colorScheme !== self::UNDEFINED) {
            $instance->colorScheme = $colorScheme;
        }
        if ($section !== self::UNDEFINED) {
            $instance->section = $section;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'backgroundImage' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingImageInput),
            'colorScheme' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'section' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingMainSectionInput),
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
