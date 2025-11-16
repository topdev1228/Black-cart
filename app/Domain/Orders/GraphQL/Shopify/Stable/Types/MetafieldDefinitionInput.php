<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $key
 * @property string $name
 * @property string $ownerType
 * @property string $type
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetafieldAccessInput|null $access
 * @property string|null $description
 * @property string|null $namespace
 * @property bool|null $pin
 * @property bool|null $useAsCollectionCondition
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetafieldDefinitionValidationInput>|null $validations
 */
class MetafieldDefinitionInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $key
     * @param string $name
     * @param string $ownerType
     * @param string $type
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetafieldAccessInput|null $access
     * @param string|null $description
     * @param string|null $namespace
     * @param bool|null $pin
     * @param bool|null $useAsCollectionCondition
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetafieldDefinitionValidationInput>|null $validations
     */
    public static function make(
        $key,
        $name,
        $ownerType,
        $type,
        $access = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $description = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $namespace = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $pin = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $useAsCollectionCondition = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $validations = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($key !== self::UNDEFINED) {
            $instance->key = $key;
        }
        if ($name !== self::UNDEFINED) {
            $instance->name = $name;
        }
        if ($ownerType !== self::UNDEFINED) {
            $instance->ownerType = $ownerType;
        }
        if ($type !== self::UNDEFINED) {
            $instance->type = $type;
        }
        if ($access !== self::UNDEFINED) {
            $instance->access = $access;
        }
        if ($description !== self::UNDEFINED) {
            $instance->description = $description;
        }
        if ($namespace !== self::UNDEFINED) {
            $instance->namespace = $namespace;
        }
        if ($pin !== self::UNDEFINED) {
            $instance->pin = $pin;
        }
        if ($useAsCollectionCondition !== self::UNDEFINED) {
            $instance->useAsCollectionCondition = $useAsCollectionCondition;
        }
        if ($validations !== self::UNDEFINED) {
            $instance->validations = $validations;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'key' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'name' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'ownerType' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'type' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'access' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetafieldAccessInput),
            'description' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'namespace' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'pin' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'useAsCollectionCondition' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'validations' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetafieldDefinitionValidationInput))),
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
