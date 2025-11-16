<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string|null $id
 * @property string|null $key
 * @property string|null $namespace
 * @property string|null $type
 * @property string|null $value
 */
class MetafieldInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string|null $id
     * @param string|null $key
     * @param string|null $namespace
     * @param string|null $type
     * @param string|null $value
     */
    public static function make(
        $id = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $key = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $namespace = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $type = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $value = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($id !== self::UNDEFINED) {
            $instance->id = $id;
        }
        if ($key !== self::UNDEFINED) {
            $instance->key = $key;
        }
        if ($namespace !== self::UNDEFINED) {
            $instance->namespace = $namespace;
        }
        if ($type !== self::UNDEFINED) {
            $instance->type = $type;
        }
        if ($value !== self::UNDEFINED) {
            $instance->value = $value;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'id' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'key' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'namespace' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'type' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'value' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
