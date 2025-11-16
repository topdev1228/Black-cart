<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property bool|null $enabled
 * @property string|null $handle
 * @property string|null $name
 */
class MarketUpdateInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param bool|null $enabled
     * @param string|null $handle
     * @param string|null $name
     */
    public static function make(
        $enabled = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $handle = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $name = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($enabled !== self::UNDEFINED) {
            $instance->enabled = $enabled;
        }
        if ($handle !== self::UNDEFINED) {
            $instance->handle = $handle;
        }
        if ($name !== self::UNDEFINED) {
            $instance->name = $name;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'enabled' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'handle' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'name' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
