<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $key
 * @property string $namespace
 * @property string $ownerType
 * @property bool|null $pin
 * @property bool|null $visibleToStorefrontApi
 */
class StandardMetafieldDefinitionsEnableInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $key
     * @param string $namespace
     * @param string $ownerType
     * @param bool|null $pin
     * @param bool|null $visibleToStorefrontApi
     */
    public static function make(
        $key,
        $namespace,
        $ownerType,
        $pin = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $visibleToStorefrontApi = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($key !== self::UNDEFINED) {
            $instance->key = $key;
        }
        if ($namespace !== self::UNDEFINED) {
            $instance->namespace = $namespace;
        }
        if ($ownerType !== self::UNDEFINED) {
            $instance->ownerType = $ownerType;
        }
        if ($pin !== self::UNDEFINED) {
            $instance->pin = $pin;
        }
        if ($visibleToStorefrontApi !== self::UNDEFINED) {
            $instance->visibleToStorefrontApi = $visibleToStorefrontApi;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'key' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'namespace' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'ownerType' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'pin' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'visibleToStorefrontApi' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
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
