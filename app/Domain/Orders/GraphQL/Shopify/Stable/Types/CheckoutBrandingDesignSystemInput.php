<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingColorsInput|null $colors
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingCornerRadiusVariablesInput|null $cornerRadius
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingTypographyInput|null $typography
 */
class CheckoutBrandingDesignSystemInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingColorsInput|null $colors
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingCornerRadiusVariablesInput|null $cornerRadius
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingTypographyInput|null $typography
     */
    public static function make(
        $colors = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $cornerRadius = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $typography = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($colors !== self::UNDEFINED) {
            $instance->colors = $colors;
        }
        if ($cornerRadius !== self::UNDEFINED) {
            $instance->cornerRadius = $cornerRadius;
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
            'colors' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingColorsInput),
            'cornerRadius' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingCornerRadiusVariablesInput),
            'typography' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingTypographyInput),
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
