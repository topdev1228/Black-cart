<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\FulfillmentOrderLineItemsInput> $lineItemsByFulfillmentOrder
 * @property bool|null $notifyCustomer
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\FulfillmentOriginAddressInput|null $originAddress
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\FulfillmentTrackingInput|null $trackingInfo
 */
class FulfillmentV2Input extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\FulfillmentOrderLineItemsInput> $lineItemsByFulfillmentOrder
     * @param bool|null $notifyCustomer
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\FulfillmentOriginAddressInput|null $originAddress
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\FulfillmentTrackingInput|null $trackingInfo
     */
    public static function make(
        $lineItemsByFulfillmentOrder,
        $notifyCustomer = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $originAddress = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $trackingInfo = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($lineItemsByFulfillmentOrder !== self::UNDEFINED) {
            $instance->lineItemsByFulfillmentOrder = $lineItemsByFulfillmentOrder;
        }
        if ($notifyCustomer !== self::UNDEFINED) {
            $instance->notifyCustomer = $notifyCustomer;
        }
        if ($originAddress !== self::UNDEFINED) {
            $instance->originAddress = $originAddress;
        }
        if ($trackingInfo !== self::UNDEFINED) {
            $instance->trackingInfo = $trackingInfo;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'lineItemsByFulfillmentOrder' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\FulfillmentOrderLineItemsInput))),
            'notifyCustomer' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'originAddress' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\FulfillmentOriginAddressInput),
            'trackingInfo' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\FulfillmentTrackingInput),
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
