<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $key
 * @property string $ownerType
 * @property string|null $namespace
 */
class MetafieldStorefrontVisibilityInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $key
     * @param string $ownerType
     * @param string|null $namespace
     */
    public static function make(
        $key,
        $ownerType,
        $namespace = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($key !== self::UNDEFINED) {
            $instance->key = $key;
        }
        if ($ownerType !== self::UNDEFINED) {
            $instance->ownerType = $ownerType;
        }
        if ($namespace !== self::UNDEFINED) {
            $instance->namespace = $namespace;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'key' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'ownerType' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'namespace' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
