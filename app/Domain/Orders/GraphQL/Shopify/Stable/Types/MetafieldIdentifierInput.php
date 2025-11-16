<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $key
 * @property string $namespace
 * @property int|string $ownerId
 */
class MetafieldIdentifierInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $key
     * @param string $namespace
     * @param int|string $ownerId
     */
    public static function make($key, $namespace, $ownerId): self
    {
        $instance = new self;

        if ($key !== self::UNDEFINED) {
            $instance->key = $key;
        }
        if ($namespace !== self::UNDEFINED) {
            $instance->namespace = $namespace;
        }
        if ($ownerId !== self::UNDEFINED) {
            $instance->ownerId = $ownerId;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'key' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'namespace' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'ownerId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
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
