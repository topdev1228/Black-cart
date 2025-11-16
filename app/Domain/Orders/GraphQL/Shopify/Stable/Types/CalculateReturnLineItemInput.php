<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string $fulfillmentLineItemId
 * @property int $quantity
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\RestockingFeeInput|null $restockingFee
 */
class CalculateReturnLineItemInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string $fulfillmentLineItemId
     * @param int $quantity
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\RestockingFeeInput|null $restockingFee
     */
    public static function make(
        $fulfillmentLineItemId,
        $quantity,
        $restockingFee = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($fulfillmentLineItemId !== self::UNDEFINED) {
            $instance->fulfillmentLineItemId = $fulfillmentLineItemId;
        }
        if ($quantity !== self::UNDEFINED) {
            $instance->quantity = $quantity;
        }
        if ($restockingFee !== self::UNDEFINED) {
            $instance->restockingFee = $restockingFee;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'fulfillmentLineItemId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'quantity' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'restockingFee' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\RestockingFeeInput),
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
