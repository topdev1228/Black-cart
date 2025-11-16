<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string|null $id
 * @property string|null $name
 * @property int|string|null $optionId
 * @property string|null $optionName
 */
class VariantOptionValueInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string|null $id
     * @param string|null $name
     * @param int|string|null $optionId
     * @param string|null $optionName
     */
    public static function make(
        $id = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $name = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $optionId = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $optionName = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($id !== self::UNDEFINED) {
            $instance->id = $id;
        }
        if ($name !== self::UNDEFINED) {
            $instance->name = $name;
        }
        if ($optionId !== self::UNDEFINED) {
            $instance->optionId = $optionId;
        }
        if ($optionName !== self::UNDEFINED) {
            $instance->optionName = $optionName;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'id' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'name' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'optionId' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'optionName' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
