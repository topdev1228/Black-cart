<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string|null $mediaImageId
 */
class CheckoutBrandingImageInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string|null $mediaImageId
     */
    public static function make(
        $mediaImageId = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($mediaImageId !== self::UNDEFINED) {
            $instance->mediaImageId = $mediaImageId;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'mediaImageId' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
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
