<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string $id
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\LinkedMetafieldUpdateInput|null $linkedMetafield
 * @property string|null $name
 * @property int|null $position
 */
class OptionUpdateInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string $id
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\LinkedMetafieldUpdateInput|null $linkedMetafield
     * @param string|null $name
     * @param int|null $position
     */
    public static function make(
        $id,
        $linkedMetafield = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $name = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $position = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($id !== self::UNDEFINED) {
            $instance->id = $id;
        }
        if ($linkedMetafield !== self::UNDEFINED) {
            $instance->linkedMetafield = $linkedMetafield;
        }
        if ($name !== self::UNDEFINED) {
            $instance->name = $name;
        }
        if ($position !== self::UNDEFINED) {
            $instance->position = $position;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'id' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'linkedMetafield' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\LinkedMetafieldUpdateInput),
            'name' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'position' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
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
