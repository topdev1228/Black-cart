<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $key
 * @property string $namespace
 */
class LinkedMetafieldUpdateInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $key
     * @param string $namespace
     */
    public static function make($key, $namespace): self
    {
        $instance = new self;

        if ($key !== self::UNDEFINED) {
            $instance->key = $key;
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
            'namespace' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
