<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionDeliveryMethodLocalDeliveryInput|null $localDelivery
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionDeliveryMethodPickupInput|null $pickup
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionDeliveryMethodShippingInput|null $shipping
 */
class SubscriptionDeliveryMethodInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionDeliveryMethodLocalDeliveryInput|null $localDelivery
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionDeliveryMethodPickupInput|null $pickup
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionDeliveryMethodShippingInput|null $shipping
     */
    public static function make(
        $localDelivery = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $pickup = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $shipping = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($localDelivery !== self::UNDEFINED) {
            $instance->localDelivery = $localDelivery;
        }
        if ($pickup !== self::UNDEFINED) {
            $instance->pickup = $pickup;
        }
        if ($shipping !== self::UNDEFINED) {
            $instance->shipping = $shipping;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'localDelivery' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionDeliveryMethodLocalDeliveryInput),
            'pickup' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionDeliveryMethodPickupInput),
            'shipping' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionDeliveryMethodShippingInput),
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
