<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string|null $border
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingTypographyStyleInput|null $typography
 */
class CheckoutBrandingTextFieldInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string|null $border
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingTypographyStyleInput|null $typography
     */
    public static function make(
        $border = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $typography = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($border !== self::UNDEFINED) {
            $instance->border = $border;
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
            'border' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
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
