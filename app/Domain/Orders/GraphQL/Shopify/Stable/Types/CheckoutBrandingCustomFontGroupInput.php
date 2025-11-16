<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingCustomFontInput $base
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingCustomFontInput $bold
 * @property string|null $loadingStrategy
 */
class CheckoutBrandingCustomFontGroupInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingCustomFontInput $base
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingCustomFontInput $bold
     * @param string|null $loadingStrategy
     */
    public static function make(
        $base,
        $bold,
        $loadingStrategy = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($base !== self::UNDEFINED) {
            $instance->base = $base;
        }
        if ($bold !== self::UNDEFINED) {
            $instance->bold = $bold;
        }
        if ($loadingStrategy !== self::UNDEFINED) {
            $instance->loadingStrategy = $loadingStrategy;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'base' => new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingCustomFontInput),
            'bold' => new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingCustomFontInput),
            'loadingStrategy' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
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
