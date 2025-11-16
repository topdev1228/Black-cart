<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingColorGlobalInput|null $global
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingColorSchemesInput|null $schemes
 */
class CheckoutBrandingColorsInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingColorGlobalInput|null $global
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingColorSchemesInput|null $schemes
     */
    public static function make(
        $global = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $schemes = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($global !== self::UNDEFINED) {
            $instance->global = $global;
        }
        if ($schemes !== self::UNDEFINED) {
            $instance->schemes = $schemes;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'global' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingColorGlobalInput),
            'schemes' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingColorSchemesInput),
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
