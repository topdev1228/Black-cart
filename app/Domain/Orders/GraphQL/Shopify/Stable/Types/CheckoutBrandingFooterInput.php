<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string|null $alignment
 * @property string|null $background
 * @property string|null $colorScheme
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingFooterContentInput|null $content
 * @property string|null $padding
 * @property string|null $position
 */
class CheckoutBrandingFooterInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string|null $alignment
     * @param string|null $background
     * @param string|null $colorScheme
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingFooterContentInput|null $content
     * @param string|null $padding
     * @param string|null $position
     */
    public static function make(
        $alignment = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $background = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $colorScheme = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $content = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
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
        if ($colorScheme !== self::UNDEFINED) {
            $instance->colorScheme = $colorScheme;
        }
        if ($content !== self::UNDEFINED) {
            $instance->content = $content;
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
            'colorScheme' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'content' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingFooterContentInput),
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
