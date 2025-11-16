<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $key
 * @property string|null $description
 * @property string|null $name
 * @property bool|null $required
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetafieldDefinitionValidationInput>|null $validations
 * @property bool|null $visibleToStorefrontApi
 */
class MetaobjectFieldDefinitionUpdateInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $key
     * @param string|null $description
     * @param string|null $name
     * @param bool|null $required
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetafieldDefinitionValidationInput>|null $validations
     * @param bool|null $visibleToStorefrontApi
     */
    public static function make(
        $key,
        $description = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $name = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $required = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $validations = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $visibleToStorefrontApi = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($key !== self::UNDEFINED) {
            $instance->key = $key;
        }
        if ($description !== self::UNDEFINED) {
            $instance->description = $description;
        }
        if ($name !== self::UNDEFINED) {
            $instance->name = $name;
        }
        if ($required !== self::UNDEFINED) {
            $instance->required = $required;
        }
        if ($validations !== self::UNDEFINED) {
            $instance->validations = $validations;
        }
        if ($visibleToStorefrontApi !== self::UNDEFINED) {
            $instance->visibleToStorefrontApi = $visibleToStorefrontApi;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'key' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'description' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'name' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'required' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'validations' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetafieldDefinitionValidationInput))),
            'visibleToStorefrontApi' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
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
