<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingCustomizationsInput|null $customizations
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingDesignSystemInput|null $designSystem
 */
class CheckoutBrandingInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingCustomizationsInput|null $customizations
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingDesignSystemInput|null $designSystem
     */
    public static function make(
        $customizations = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $designSystem = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($customizations !== self::UNDEFINED) {
            $instance->customizations = $customizations;
        }
        if ($designSystem !== self::UNDEFINED) {
            $instance->designSystem = $designSystem;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'customizations' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingCustomizationsInput),
            'designSystem' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingDesignSystemInput),
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
