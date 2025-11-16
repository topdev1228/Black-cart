<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int $height
 * @property int $left
 * @property int $top
 * @property int $width
 */
class CropRegionInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int $height
     * @param int $left
     * @param int $top
     * @param int $width
     */
    public static function make($height, $left, $top, $width): self
    {
        $instance = new self;

        if ($height !== self::UNDEFINED) {
            $instance->height = $height;
        }
        if ($left !== self::UNDEFINED) {
            $instance->left = $left;
        }
        if ($top !== self::UNDEFINED) {
            $instance->top = $top;
        }
        if ($width !== self::UNDEFINED) {
            $instance->width = $width;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'height' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'left' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'top' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'width' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IntConverter),
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
