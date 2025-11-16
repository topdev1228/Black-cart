<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectCapabilityDataInput|null $capabilities
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectFieldInput>|null $fields
 * @property string|null $handle
 */
class MetaobjectUpsertInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectCapabilityDataInput|null $capabilities
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectFieldInput>|null $fields
     * @param string|null $handle
     */
    public static function make(
        $capabilities = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $fields = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $handle = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($capabilities !== self::UNDEFINED) {
            $instance->capabilities = $capabilities;
        }
        if ($fields !== self::UNDEFINED) {
            $instance->fields = $fields;
        }
        if ($handle !== self::UNDEFINED) {
            $instance->handle = $handle;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'capabilities' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectCapabilityDataInput),
            'fields' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectFieldInput))),
            'handle' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
