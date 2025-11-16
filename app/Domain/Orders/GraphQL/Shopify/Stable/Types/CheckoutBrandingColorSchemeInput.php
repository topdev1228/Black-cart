<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingColorRolesInput|null $base
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingControlColorRolesInput|null $control
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingButtonColorRolesInput|null $primaryButton
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingButtonColorRolesInput|null $secondaryButton
 */
class CheckoutBrandingColorSchemeInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingColorRolesInput|null $base
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingControlColorRolesInput|null $control
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingButtonColorRolesInput|null $primaryButton
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingButtonColorRolesInput|null $secondaryButton
     */
    public static function make(
        $base = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $control = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $primaryButton = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $secondaryButton = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($base !== self::UNDEFINED) {
            $instance->base = $base;
        }
        if ($control !== self::UNDEFINED) {
            $instance->control = $control;
        }
        if ($primaryButton !== self::UNDEFINED) {
            $instance->primaryButton = $primaryButton;
        }
        if ($secondaryButton !== self::UNDEFINED) {
            $instance->secondaryButton = $secondaryButton;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'base' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingColorRolesInput),
            'control' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingControlColorRolesInput),
            'primaryButton' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingButtonColorRolesInput),
            'secondaryButton' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingButtonColorRolesInput),
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
