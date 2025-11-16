<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string $fulfillmentLineItemId
 * @property int $quantity
 * @property string $returnReason
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\RestockingFeeInput|null $restockingFee
 * @property string|null $returnReasonNote
 */
class ReturnLineItemInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string $fulfillmentLineItemId
     * @param int $quantity
     * @param string $returnReason
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\RestockingFeeInput|null $restockingFee
     * @param string|null $returnReasonNote
     */
    public static function make(
        $fulfillmentLineItemId,
        $quantity,
        $returnReason,
        $restockingFee = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $returnReasonNote = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($fulfillmentLineItemId !== self::UNDEFINED) {
            $instance->fulfillmentLineItemId = $fulfillmentLineItemId;
        }
        if ($quantity !== self::UNDEFINED) {
            $instance->quantity = $quantity;
        }
        if ($returnReason !== self::UNDEFINED) {
            $instance->returnReason = $returnReason;
        }
        if ($restockingFee !== self::UNDEFINED) {
            $instance->restockingFee = $restockingFee;
        }
        if ($returnReasonNote !== self::UNDEFINED) {
            $instance->returnReasonNote = $returnReasonNote;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'fulfillmentLineItemId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'quantity' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'returnReason' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'restockingFee' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\RestockingFeeInput),
            'returnReasonNote' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
