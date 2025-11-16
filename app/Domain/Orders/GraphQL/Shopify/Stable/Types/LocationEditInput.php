<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\LocationEditAddressInput|null $address
 * @property bool|null $fulfillsOnlineOrders
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetafieldInput>|null $metafields
 * @property string|null $name
 */
class LocationEditInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\LocationEditAddressInput|null $address
     * @param bool|null $fulfillsOnlineOrders
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetafieldInput>|null $metafields
     * @param string|null $name
     */
    public static function make(
        $address = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $fulfillsOnlineOrders = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $metafields = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $name = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($address !== self::UNDEFINED) {
            $instance->address = $address;
        }
        if ($fulfillsOnlineOrders !== self::UNDEFINED) {
            $instance->fulfillsOnlineOrders = $fulfillsOnlineOrders;
        }
        if ($metafields !== self::UNDEFINED) {
            $instance->metafields = $metafields;
        }
        if ($name !== self::UNDEFINED) {
            $instance->name = $name;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'address' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\LocationEditAddressInput),
            'fulfillsOnlineOrders' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'metafields' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetafieldInput))),
            'name' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
