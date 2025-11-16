<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property mixed|null $cost
 * @property string|null $harmonizedSystemCode
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\InventoryItemMeasurementInput|null $measurement
 * @property bool|null $requiresShipping
 * @property bool|null $tracked
 */
class InventoryItemInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param mixed|null $cost
     * @param string|null $harmonizedSystemCode
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\InventoryItemMeasurementInput|null $measurement
     * @param bool|null $requiresShipping
     * @param bool|null $tracked
     */
    public static function make(
        $cost = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $harmonizedSystemCode = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $measurement = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $requiresShipping = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $tracked = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($cost !== self::UNDEFINED) {
            $instance->cost = $cost;
        }
        if ($harmonizedSystemCode !== self::UNDEFINED) {
            $instance->harmonizedSystemCode = $harmonizedSystemCode;
        }
        if ($measurement !== self::UNDEFINED) {
            $instance->measurement = $measurement;
        }
        if ($requiresShipping !== self::UNDEFINED) {
            $instance->requiresShipping = $requiresShipping;
        }
        if ($tracked !== self::UNDEFINED) {
            $instance->tracked = $tracked;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'cost' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'harmonizedSystemCode' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'measurement' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\InventoryItemMeasurementInput),
            'requiresShipping' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'tracked' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
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
