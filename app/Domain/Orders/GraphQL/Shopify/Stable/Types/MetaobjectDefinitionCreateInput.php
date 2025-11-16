<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $type
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectAccessInput|null $access
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectCapabilityCreateInput|null $capabilities
 * @property string|null $description
 * @property string|null $displayNameKey
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectFieldDefinitionCreateInput>|null $fieldDefinitions
 * @property string|null $name
 * @property bool|null $visibleToStorefrontApi
 */
class MetaobjectDefinitionCreateInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $type
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectAccessInput|null $access
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectCapabilityCreateInput|null $capabilities
     * @param string|null $description
     * @param string|null $displayNameKey
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectFieldDefinitionCreateInput>|null $fieldDefinitions
     * @param string|null $name
     * @param bool|null $visibleToStorefrontApi
     */
    public static function make(
        $type,
        $access = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $capabilities = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $description = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $displayNameKey = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $fieldDefinitions = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $name = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $visibleToStorefrontApi = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($type !== self::UNDEFINED) {
            $instance->type = $type;
        }
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
        if ($visibleToStorefrontApi !== self::UNDEFINED) {
            $instance->visibleToStorefrontApi = $visibleToStorefrontApi;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'type' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'access' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectAccessInput),
            'capabilities' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectCapabilityCreateInput),
            'description' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'displayNameKey' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'fieldDefinitions' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectFieldDefinitionCreateInput))),
            'name' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
