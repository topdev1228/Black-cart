<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\LinkedMetafieldCreateInput|null $linkedMetafield
 * @property string|null $name
 * @property int|null $position
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\OptionValueCreateInput>|null $values
 */
class OptionCreateInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\LinkedMetafieldCreateInput|null $linkedMetafield
     * @param string|null $name
     * @param int|null $position
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\OptionValueCreateInput>|null $values
     */
    public static function make(
        $linkedMetafield = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $name = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $position = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $values = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($linkedMetafield !== self::UNDEFINED) {
            $instance->linkedMetafield = $linkedMetafield;
        }
        if ($name !== self::UNDEFINED) {
            $instance->name = $name;
        }
        if ($position !== self::UNDEFINED) {
            $instance->position = $position;
        }
        if ($values !== self::UNDEFINED) {
            $instance->values = $values;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'linkedMetafield' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\LinkedMetafieldCreateInput),
            'name' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'position' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'values' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\OptionValueCreateInput))),
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
