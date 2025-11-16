<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string|null $id
 * @property string|null $name
 * @property int|null $position
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\OptionValueSetInput>|null $values
 */
class OptionSetInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string|null $id
     * @param string|null $name
     * @param int|null $position
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\OptionValueSetInput>|null $values
     */
    public static function make(
        $id = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $name = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $position = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $values = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($id !== self::UNDEFINED) {
            $instance->id = $id;
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
            'id' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'name' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'position' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'values' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\OptionValueSetInput))),
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
