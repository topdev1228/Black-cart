<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingCustomFontGroupInput|null $customFontGroup
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingShopifyFontGroupInput|null $shopifyFontGroup
 */
class CheckoutBrandingFontGroupInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingCustomFontGroupInput|null $customFontGroup
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingShopifyFontGroupInput|null $shopifyFontGroup
     */
    public static function make(
        $customFontGroup = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $shopifyFontGroup = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($customFontGroup !== self::UNDEFINED) {
            $instance->customFontGroup = $customFontGroup;
        }
        if ($shopifyFontGroup !== self::UNDEFINED) {
            $instance->shopifyFontGroup = $shopifyFontGroup;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'customFontGroup' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingCustomFontGroupInput),
            'shopifyFontGroup' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingShopifyFontGroupInput),
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
