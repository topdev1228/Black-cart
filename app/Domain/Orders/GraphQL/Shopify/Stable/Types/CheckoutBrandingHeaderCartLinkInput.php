<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string|null $contentType
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingImageInput|null $image
 */
class CheckoutBrandingHeaderCartLinkInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string|null $contentType
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingImageInput|null $image
     */
    public static function make(
        $contentType = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $image = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($contentType !== self::UNDEFINED) {
            $instance->contentType = $contentType;
        }
        if ($image !== self::UNDEFINED) {
            $instance->image = $image;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'contentType' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'image' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CheckoutBrandingImageInput),
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
