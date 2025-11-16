<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property bool $enabled
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectCapabilityDefinitionDataRenderableInput|null $data
 */
class MetaobjectCapabilityRenderableInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param bool $enabled
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectCapabilityDefinitionDataRenderableInput|null $data
     */
    public static function make(
        $enabled,
        $data = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($enabled !== self::UNDEFINED) {
            $instance->enabled = $enabled;
        }
        if ($data !== self::UNDEFINED) {
            $instance->data = $data;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'enabled' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'data' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectCapabilityDefinitionDataRenderableInput),
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
