<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string|null $accent
 * @property string|null $background
 * @property string|null $border
 * @property string|null $decorative
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingColorRolesInput|null $hover
 * @property string|null $icon
 * @property string|null $text
 */
class CheckoutBrandingButtonColorRolesInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string|null $accent
     * @param string|null $background
     * @param string|null $border
     * @param string|null $decorative
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingColorRolesInput|null $hover
     * @param string|null $icon
     * @param string|null $text
     */
    public static function make(
        $accent = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $background = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $border = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $decorative = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $hover = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $icon = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $text = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($accent !== self::UNDEFINED) {
            $instance->accent = $accent;
        }
        if ($background !== self::UNDEFINED) {
            $instance->background = $background;
        }
        if ($border !== self::UNDEFINED) {
            $instance->border = $border;
        }
        if ($decorative !== self::UNDEFINED) {
            $instance->decorative = $decorative;
        }
        if ($hover !== self::UNDEFINED) {
            $instance->hover = $hover;
        }
        if ($icon !== self::UNDEFINED) {
            $instance->icon = $icon;
        }
        if ($text !== self::UNDEFINED) {
            $instance->text = $text;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'accent' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'background' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'border' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'decorative' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'hover' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingColorRolesInput),
            'icon' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'text' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
