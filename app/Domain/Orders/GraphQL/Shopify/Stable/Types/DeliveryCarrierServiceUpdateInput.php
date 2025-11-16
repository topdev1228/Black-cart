<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string $id
 * @property bool|null $active
 * @property mixed|null $callbackUrl
 * @property string|null $name
 * @property bool|null $supportsServiceDiscovery
 */
class DeliveryCarrierServiceUpdateInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string $id
     * @param bool|null $active
     * @param mixed|null $callbackUrl
     * @param string|null $name
     * @param bool|null $supportsServiceDiscovery
     */
    public static function make(
        $id,
        $active = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $callbackUrl = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $name = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $supportsServiceDiscovery = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($id !== self::UNDEFINED) {
            $instance->id = $id;
        }
        if ($active !== self::UNDEFINED) {
            $instance->active = $active;
        }
        if ($callbackUrl !== self::UNDEFINED) {
            $instance->callbackUrl = $callbackUrl;
        }
        if ($name !== self::UNDEFINED) {
            $instance->name = $name;
        }
        if ($supportsServiceDiscovery !== self::UNDEFINED) {
            $instance->supportsServiceDiscovery = $supportsServiceDiscovery;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'id' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'active' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'callbackUrl' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'name' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'supportsServiceDiscovery' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
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
