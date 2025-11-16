<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectCapabilityOnlineStoreInput|null $onlineStore
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectCapabilityPublishableInput|null $publishable
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectCapabilityRenderableInput|null $renderable
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectCapabilityTranslatableInput|null $translatable
 */
class MetaobjectCapabilityCreateInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectCapabilityOnlineStoreInput|null $onlineStore
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectCapabilityPublishableInput|null $publishable
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectCapabilityRenderableInput|null $renderable
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectCapabilityTranslatableInput|null $translatable
     */
    public static function make(
        $onlineStore = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $publishable = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $renderable = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $translatable = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($onlineStore !== self::UNDEFINED) {
            $instance->onlineStore = $onlineStore;
        }
        if ($publishable !== self::UNDEFINED) {
            $instance->publishable = $publishable;
        }
        if ($renderable !== self::UNDEFINED) {
            $instance->renderable = $renderable;
        }
        if ($translatable !== self::UNDEFINED) {
            $instance->translatable = $translatable;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'onlineStore' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectCapabilityOnlineStoreInput),
            'publishable' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectCapabilityPublishableInput),
            'renderable' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectCapabilityRenderableInput),
            'translatable' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectCapabilityTranslatableInput),
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
