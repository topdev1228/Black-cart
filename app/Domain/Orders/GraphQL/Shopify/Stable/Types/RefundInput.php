<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string $orderId
 * @property string|null $currency
 * @property string|null $note
 * @property bool|null $notify
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\RefundAdditionalFeeInput>|null $refundAdditionalFees
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\RefundDutyInput>|null $refundDuties
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\RefundLineItemInput>|null $refundLineItems
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ShippingRefundInput|null $shipping
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\OrderTransactionInput>|null $transactions
 */
class RefundInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string $orderId
     * @param string|null $currency
     * @param string|null $note
     * @param bool|null $notify
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\RefundAdditionalFeeInput>|null $refundAdditionalFees
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\RefundDutyInput>|null $refundDuties
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\RefundLineItemInput>|null $refundLineItems
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ShippingRefundInput|null $shipping
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\OrderTransactionInput>|null $transactions
     */
    public static function make(
        $orderId,
        $currency = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $note = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $notify = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $refundAdditionalFees = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $refundDuties = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $refundLineItems = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $shipping = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $transactions = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($orderId !== self::UNDEFINED) {
            $instance->orderId = $orderId;
        }
        if ($currency !== self::UNDEFINED) {
            $instance->currency = $currency;
        }
        if ($note !== self::UNDEFINED) {
            $instance->note = $note;
        }
        if ($notify !== self::UNDEFINED) {
            $instance->notify = $notify;
        }
        if ($refundAdditionalFees !== self::UNDEFINED) {
            $instance->refundAdditionalFees = $refundAdditionalFees;
        }
        if ($refundDuties !== self::UNDEFINED) {
            $instance->refundDuties = $refundDuties;
        }
        if ($refundLineItems !== self::UNDEFINED) {
            $instance->refundLineItems = $refundLineItems;
        }
        if ($shipping !== self::UNDEFINED) {
            $instance->shipping = $shipping;
        }
        if ($transactions !== self::UNDEFINED) {
            $instance->transactions = $transactions;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'orderId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'currency' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'note' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'notify' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'refundAdditionalFees' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\RefundAdditionalFeeInput))),
            'refundDuties' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\RefundDutyInput))),
            'refundLineItems' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\RefundLineItemInput))),
            'shipping' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ShippingRefundInput),
            'transactions' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\OrderTransactionInput))),
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
