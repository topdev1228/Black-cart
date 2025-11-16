<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property bool|null $default
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ObjectDimensionsInput|null $dimensions
 * @property string|null $name
 * @property string|null $type
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\WeightInput|null $weight
 */
class CustomShippingPackageInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param bool|null $default
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ObjectDimensionsInput|null $dimensions
     * @param string|null $name
     * @param string|null $type
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\WeightInput|null $weight
     */
    public static function make(
        $default = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $dimensions = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $name = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $type = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $weight = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($default !== self::UNDEFINED) {
            $instance->default = $default;
        }
        if ($dimensions !== self::UNDEFINED) {
            $instance->dimensions = $dimensions;
        }
        if ($name !== self::UNDEFINED) {
            $instance->name = $name;
        }
        if ($type !== self::UNDEFINED) {
            $instance->type = $type;
        }
        if ($weight !== self::UNDEFINED) {
            $instance->weight = $weight;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'default' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'dimensions' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ObjectDimensionsInput),
            'name' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'type' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'weight' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\WeightInput),
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
