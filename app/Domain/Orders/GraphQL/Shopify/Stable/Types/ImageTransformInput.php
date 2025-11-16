<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string|null $crop
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CropRegionInput|null $cropRegion
 * @property int|null $maxHeight
 * @property int|null $maxWidth
 * @property string|null $preferredContentType
 * @property int|null $scale
 */
class ImageTransformInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string|null $crop
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CropRegionInput|null $cropRegion
     * @param int|null $maxHeight
     * @param int|null $maxWidth
     * @param string|null $preferredContentType
     * @param int|null $scale
     */
    public static function make(
        $crop = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $cropRegion = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $maxHeight = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $maxWidth = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $preferredContentType = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $scale = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($crop !== self::UNDEFINED) {
            $instance->crop = $crop;
        }
        if ($cropRegion !== self::UNDEFINED) {
            $instance->cropRegion = $cropRegion;
        }
        if ($maxHeight !== self::UNDEFINED) {
            $instance->maxHeight = $maxHeight;
        }
        if ($maxWidth !== self::UNDEFINED) {
            $instance->maxWidth = $maxWidth;
        }
        if ($preferredContentType !== self::UNDEFINED) {
            $instance->preferredContentType = $preferredContentType;
        }
        if ($scale !== self::UNDEFINED) {
            $instance->scale = $scale;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'crop' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'cropRegion' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CropRegionInput),
            'maxHeight' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'maxWidth' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'preferredContentType' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'scale' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
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
