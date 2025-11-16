<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $key
 * @property string $namespace
 * @property int|string|null $owner
 */
class PrivateMetafieldDeleteInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $key
     * @param string $namespace
     * @param int|string|null $owner
     */
    public static function make(
        $key,
        $namespace,
        $owner = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($key !== self::UNDEFINED) {
            $instance->key = $key;
        }
        if ($namespace !== self::UNDEFINED) {
            $instance->namespace = $namespace;
        }
        if ($owner !== self::UNDEFINED) {
            $instance->owner = $owner;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'key' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'namespace' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'owner' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
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
