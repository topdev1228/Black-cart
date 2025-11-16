<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $key
 * @property int|string $ownerId
 * @property string $value
 * @property string|null $namespace
 * @property string|null $type
 */
class MetafieldsSetInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $key
     * @param int|string $ownerId
     * @param string $value
     * @param string|null $namespace
     * @param string|null $type
     */
    public static function make(
        $key,
        $ownerId,
        $value,
        $namespace = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $type = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($key !== self::UNDEFINED) {
            $instance->key = $key;
        }
        if ($ownerId !== self::UNDEFINED) {
            $instance->ownerId = $ownerId;
        }
        if ($value !== self::UNDEFINED) {
            $instance->value = $value;
        }
        if ($namespace !== self::UNDEFINED) {
            $instance->namespace = $namespace;
        }
        if ($type !== self::UNDEFINED) {
            $instance->type = $type;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'key' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'ownerId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'value' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'namespace' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'type' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
