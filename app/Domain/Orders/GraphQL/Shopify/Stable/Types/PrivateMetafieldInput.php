<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $key
 * @property string $namespace
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PrivateMetafieldValueInput $valueInput
 * @property int|string|null $owner
 */
class PrivateMetafieldInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $key
     * @param string $namespace
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PrivateMetafieldValueInput $valueInput
     * @param int|string|null $owner
     */
    public static function make(
        $key,
        $namespace,
        $valueInput,
        $owner = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($key !== self::UNDEFINED) {
            $instance->key = $key;
        }
        if ($namespace !== self::UNDEFINED) {
            $instance->namespace = $namespace;
        }
        if ($valueInput !== self::UNDEFINED) {
            $instance->valueInput = $valueInput;
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
            'valueInput' => new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PrivateMetafieldValueInput),
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
