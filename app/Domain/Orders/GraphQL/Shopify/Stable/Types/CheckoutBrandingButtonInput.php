<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string|null $background
 * @property string|null $blockPadding
 * @property string|null $border
 * @property string|null $cornerRadius
 * @property string|null $inlinePadding
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingTypographyStyleInput|null $typography
 */
class CheckoutBrandingButtonInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string|null $background
     * @param string|null $blockPadding
     * @param string|null $border
     * @param string|null $cornerRadius
     * @param string|null $inlinePadding
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingTypographyStyleInput|null $typography
     */
    public static function make(
        $background = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $blockPadding = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $border = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $cornerRadius = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $inlinePadding = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $typography = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($background !== self::UNDEFINED) {
            $instance->background = $background;
        }
        if ($blockPadding !== self::UNDEFINED) {
            $instance->blockPadding = $blockPadding;
        }
        if ($border !== self::UNDEFINED) {
            $instance->border = $border;
        }
        if ($cornerRadius !== self::UNDEFINED) {
            $instance->cornerRadius = $cornerRadius;
        }
        if ($inlinePadding !== self::UNDEFINED) {
            $instance->inlinePadding = $inlinePadding;
        }
        if ($typography !== self::UNDEFINED) {
            $instance->typography = $typography;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'background' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'blockPadding' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'border' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'cornerRadius' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'inlinePadding' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'typography' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingTypographyStyleInput),
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
