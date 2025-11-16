<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectAccessInput|null $access
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectCapabilityUpdateInput|null $capabilities
 * @property string|null $description
 * @property string|null $displayNameKey
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectFieldDefinitionOperationInput>|null $fieldDefinitions
 * @property string|null $name
 * @property bool|null $resetFieldOrder
 * @property bool|null $visibleToStorefrontApi
 */
class MetaobjectDefinitionUpdateInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectAccessInput|null $access
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectCapabilityUpdateInput|null $capabilities
     * @param string|null $description
     * @param string|null $displayNameKey
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectFieldDefinitionOperationInput>|null $fieldDefinitions
     * @param string|null $name
     * @param bool|null $resetFieldOrder
     * @param bool|null $visibleToStorefrontApi
     */
    public static function make(
        $access = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $capabilities = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $description = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $displayNameKey = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $fieldDefinitions = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $name = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $resetFieldOrder = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $visibleToStorefrontApi = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($access !== self::UNDEFINED) {
            $instance->access = $access;
        }
        if ($capabilities !== self::UNDEFINED) {
            $instance->capabilities = $capabilities;
        }
        if ($description !== self::UNDEFINED) {
            $instance->description = $description;
        }
        if ($displayNameKey !== self::UNDEFINED) {
            $instance->displayNameKey = $displayNameKey;
        }
        if ($fieldDefinitions !== self::UNDEFINED) {
            $instance->fieldDefinitions = $fieldDefinitions;
        }
        if ($name !== self::UNDEFINED) {
            $instance->name = $name;
        }
        if ($resetFieldOrder !== self::UNDEFINED) {
            $instance->resetFieldOrder = $resetFieldOrder;
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
            'access' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectAccessInput),
            'capabilities' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectCapabilityUpdateInput),
            'description' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'displayNameKey' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'fieldDefinitions' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectFieldDefinitionOperationInput))),
            'name' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'resetFieldOrder' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
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
