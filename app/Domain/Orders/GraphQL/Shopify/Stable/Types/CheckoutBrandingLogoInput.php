<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingImageInput|null $image
 * @property int|null $maxWidth
 * @property string|null $visibility
 */
class CheckoutBrandingLogoInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingImageInput|null $image
     * @param int|null $maxWidth
     * @param string|null $visibility
     */
    public static function make(
        $image = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $maxWidth = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $visibility = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($image !== self::UNDEFINED) {
            $instance->image = $image;
        }
        if ($maxWidth !== self::UNDEFINED) {
            $instance->maxWidth = $maxWidth;
        }
        if ($visibility !== self::UNDEFINED) {
            $instance->visibility = $visibility;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'image' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingImageInput),
            'maxWidth' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'visibility' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
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
