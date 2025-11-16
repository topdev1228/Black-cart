<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int $quantity
 * @property int|string $reverseFulfillmentOrderLineItemId
 */
class ReverseDeliveryLineItemInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int $quantity
     * @param int|string $reverseFulfillmentOrderLineItemId
     */
    public static function make($quantity, $reverseFulfillmentOrderLineItemId): self
    {
        $instance = new self;

        if ($quantity !== self::UNDEFINED) {
            $instance->quantity = $quantity;
        }
        if ($reverseFulfillmentOrderLineItemId !== self::UNDEFINED) {
            $instance->reverseFulfillmentOrderLineItemId = $reverseFulfillmentOrderLineItemId;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'quantity' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'reverseFulfillmentOrderLineItemId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
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
