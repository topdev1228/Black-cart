<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property float|int $height
 * @property float|int $length
 * @property string $unit
 * @property float|int $width
 */
class ObjectDimensionsInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param float|int $height
     * @param float|int $length
     * @param string $unit
     * @param float|int $width
     */
    public static function make($height, $length, $unit, $width): self
    {
        $instance = new self;

        if ($height !== self::UNDEFINED) {
            $instance->height = $height;
        }
        if ($length !== self::UNDEFINED) {
            $instance->length = $length;
        }
        if ($unit !== self::UNDEFINED) {
            $instance->unit = $unit;
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
            'height' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\FloatConverter),
            'length' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\FloatConverter),
            'unit' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'width' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\FloatConverter),
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
