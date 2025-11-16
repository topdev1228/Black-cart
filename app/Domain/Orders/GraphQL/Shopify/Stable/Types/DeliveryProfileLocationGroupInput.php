<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string|null $id
 * @property array<int|string>|null $locations
 * @property array<int|string>|null $locationsToAdd
 * @property array<int|string>|null $locationsToRemove
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\DeliveryLocationGroupZoneInput>|null $zonesToCreate
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\DeliveryLocationGroupZoneInput>|null $zonesToUpdate
 */
class DeliveryProfileLocationGroupInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string|null $id
     * @param array<int|string>|null $locations
     * @param array<int|string>|null $locationsToAdd
     * @param array<int|string>|null $locationsToRemove
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\DeliveryLocationGroupZoneInput>|null $zonesToCreate
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\DeliveryLocationGroupZoneInput>|null $zonesToUpdate
     */
    public static function make(
        $id = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $locations = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $locationsToAdd = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $locationsToRemove = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $zonesToCreate = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $zonesToUpdate = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($id !== self::UNDEFINED) {
            $instance->id = $id;
        }
        if ($locations !== self::UNDEFINED) {
            $instance->locations = $locations;
        }
        if ($locationsToAdd !== self::UNDEFINED) {
            $instance->locationsToAdd = $locationsToAdd;
        }
        if ($locationsToRemove !== self::UNDEFINED) {
            $instance->locationsToRemove = $locationsToRemove;
        }
        if ($zonesToCreate !== self::UNDEFINED) {
            $instance->zonesToCreate = $zonesToCreate;
        }
        if ($zonesToUpdate !== self::UNDEFINED) {
            $instance->zonesToUpdate = $zonesToUpdate;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'id' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'locations' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter))),
            'locationsToAdd' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter))),
            'locationsToRemove' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter))),
            'zonesToCreate' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DeliveryLocationGroupZoneInput))),
            'zonesToUpdate' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DeliveryLocationGroupZoneInput))),
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
