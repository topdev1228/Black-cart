<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string|null $background
 * @property string|null $border
 * @property string|null $borderStyle
 * @property string|null $borderWidth
 * @property string|null $colorScheme
 * @property string|null $cornerRadius
 * @property string|null $padding
 * @property string|null $shadow
 */
class CheckoutBrandingMainSectionInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string|null $background
     * @param string|null $border
     * @param string|null $borderStyle
     * @param string|null $borderWidth
     * @param string|null $colorScheme
     * @param string|null $cornerRadius
     * @param string|null $padding
     * @param string|null $shadow
     */
    public static function make(
        $background = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $border = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $borderStyle = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $borderWidth = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $colorScheme = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $cornerRadius = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $padding = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $shadow = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($background !== self::UNDEFINED) {
            $instance->background = $background;
        }
        if ($border !== self::UNDEFINED) {
            $instance->border = $border;
        }
        if ($borderStyle !== self::UNDEFINED) {
            $instance->borderStyle = $borderStyle;
        }
        if ($borderWidth !== self::UNDEFINED) {
            $instance->borderWidth = $borderWidth;
        }
        if ($colorScheme !== self::UNDEFINED) {
            $instance->colorScheme = $colorScheme;
        }
        if ($cornerRadius !== self::UNDEFINED) {
            $instance->cornerRadius = $cornerRadius;
        }
        if ($padding !== self::UNDEFINED) {
            $instance->padding = $padding;
        }
        if ($shadow !== self::UNDEFINED) {
            $instance->shadow = $shadow;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'background' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'border' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'borderStyle' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'borderWidth' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'colorScheme' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'cornerRadius' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'padding' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'shadow' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
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
