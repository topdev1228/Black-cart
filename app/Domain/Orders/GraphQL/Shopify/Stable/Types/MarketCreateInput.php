<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $name
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\MarketRegionCreateInput> $regions
 * @property bool|null $enabled
 * @property string|null $handle
 */
class MarketCreateInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $name
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\MarketRegionCreateInput> $regions
     * @param bool|null $enabled
     * @param string|null $handle
     */
    public static function make(
        $name,
        $regions,
        $enabled = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $handle = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($name !== self::UNDEFINED) {
            $instance->name = $name;
        }
        if ($regions !== self::UNDEFINED) {
            $instance->regions = $regions;
        }
        if ($enabled !== self::UNDEFINED) {
            $instance->enabled = $enabled;
        }
        if ($handle !== self::UNDEFINED) {
            $instance->handle = $handle;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'name' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'regions' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MarketRegionCreateInput))),
            'enabled' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'handle' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
