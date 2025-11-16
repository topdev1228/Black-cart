<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $reason
 * @property string|null $externalId
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\FulfillmentOrderLineItemInput>|null $fulfillmentOrderLineItems
 * @property bool|null $notifyMerchant
 * @property string|null $reasonNotes
 */
class FulfillmentOrderHoldInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $reason
     * @param string|null $externalId
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\FulfillmentOrderLineItemInput>|null $fulfillmentOrderLineItems
     * @param bool|null $notifyMerchant
     * @param string|null $reasonNotes
     */
    public static function make(
        $reason,
        $externalId = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $fulfillmentOrderLineItems = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $notifyMerchant = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $reasonNotes = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($reason !== self::UNDEFINED) {
            $instance->reason = $reason;
        }
        if ($externalId !== self::UNDEFINED) {
            $instance->externalId = $externalId;
        }
        if ($fulfillmentOrderLineItems !== self::UNDEFINED) {
            $instance->fulfillmentOrderLineItems = $fulfillmentOrderLineItems;
        }
        if ($notifyMerchant !== self::UNDEFINED) {
            $instance->notifyMerchant = $notifyMerchant;
        }
        if ($reasonNotes !== self::UNDEFINED) {
            $instance->reasonNotes = $reasonNotes;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'reason' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'externalId' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'fulfillmentOrderLineItems' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\FulfillmentOrderLineItemInput))),
            'notifyMerchant' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'reasonNotes' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
