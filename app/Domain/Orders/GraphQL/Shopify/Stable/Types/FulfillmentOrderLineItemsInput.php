<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string $fulfillmentOrderId
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\FulfillmentOrderLineItemInput>|null $fulfillmentOrderLineItems
 */
class FulfillmentOrderLineItemsInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string $fulfillmentOrderId
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\FulfillmentOrderLineItemInput>|null $fulfillmentOrderLineItems
     */
    public static function make(
        $fulfillmentOrderId,
        $fulfillmentOrderLineItems = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($fulfillmentOrderId !== self::UNDEFINED) {
            $instance->fulfillmentOrderId = $fulfillmentOrderId;
        }
        if ($fulfillmentOrderLineItems !== self::UNDEFINED) {
            $instance->fulfillmentOrderLineItems = $fulfillmentOrderLineItems;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'fulfillmentOrderId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'fulfillmentOrderLineItems' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\FulfillmentOrderLineItemInput))),
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
