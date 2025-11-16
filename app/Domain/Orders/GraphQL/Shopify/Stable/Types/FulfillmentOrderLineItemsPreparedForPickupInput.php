<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\PreparedFulfillmentOrderLineItemsInput> $lineItemsByFulfillmentOrder
 */
class FulfillmentOrderLineItemsPreparedForPickupInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\PreparedFulfillmentOrderLineItemsInput> $lineItemsByFulfillmentOrder
     */
    public static function make($lineItemsByFulfillmentOrder): self
    {
        $instance = new self;

        if ($lineItemsByFulfillmentOrder !== self::UNDEFINED) {
            $instance->lineItemsByFulfillmentOrder = $lineItemsByFulfillmentOrder;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'lineItemsByFulfillmentOrder' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PreparedFulfillmentOrderLineItemsInput))),
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
