<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingColorSchemeInput|null $scheme1
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingColorSchemeInput|null $scheme2
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingColorSchemeInput|null $scheme3
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingColorSchemeInput|null $scheme4
 */
class CheckoutBrandingColorSchemesInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingColorSchemeInput|null $scheme1
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingColorSchemeInput|null $scheme2
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingColorSchemeInput|null $scheme3
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingColorSchemeInput|null $scheme4
     */
    public static function make(
        $scheme1 = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $scheme2 = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $scheme3 = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $scheme4 = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($scheme1 !== self::UNDEFINED) {
            $instance->scheme1 = $scheme1;
        }
        if ($scheme2 !== self::UNDEFINED) {
            $instance->scheme2 = $scheme2;
        }
        if ($scheme3 !== self::UNDEFINED) {
            $instance->scheme3 = $scheme3;
        }
        if ($scheme4 !== self::UNDEFINED) {
            $instance->scheme4 = $scheme4;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'scheme1' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingColorSchemeInput),
            'scheme2' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingColorSchemeInput),
            'scheme3' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingColorSchemeInput),
            'scheme4' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingColorSchemeInput),
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
