<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string|null $cornerRadius
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingTypographyStyleGlobalInput|null $typography
 */
class CheckoutBrandingGlobalInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string|null $cornerRadius
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingTypographyStyleGlobalInput|null $typography
     */
    public static function make(
        $cornerRadius = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $typography = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

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
            'cornerRadius' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'typography' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingTypographyStyleGlobalInput),
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
