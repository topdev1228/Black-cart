<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectCapabilityDataOnlineStoreInput|null $onlineStore
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectCapabilityDataPublishableInput|null $publishable
 */
class MetaobjectCapabilityDataInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectCapabilityDataOnlineStoreInput|null $onlineStore
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectCapabilityDataPublishableInput|null $publishable
     */
    public static function make(
        $onlineStore = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $publishable = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($onlineStore !== self::UNDEFINED) {
            $instance->onlineStore = $onlineStore;
        }
        if ($publishable !== self::UNDEFINED) {
            $instance->publishable = $publishable;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'onlineStore' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectCapabilityDataOnlineStoreInput),
            'publishable' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectCapabilityDataPublishableInput),
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
