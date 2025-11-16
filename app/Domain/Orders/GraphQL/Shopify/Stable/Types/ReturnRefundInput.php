<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string $returnId
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\ReturnRefundLineItemInput> $returnRefundLineItems
 * @property bool|null $applyDeductions
 * @property string|null $note
 * @property bool|null $notifyCustomer
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\ReturnRefundOrderTransactionInput>|null $orderTransactions
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\RefundDutyInput>|null $refundDuties
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\RefundShippingInput|null $refundShipping
 */
class ReturnRefundInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string $returnId
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\ReturnRefundLineItemInput> $returnRefundLineItems
     * @param bool|null $applyDeductions
     * @param string|null $note
     * @param bool|null $notifyCustomer
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\ReturnRefundOrderTransactionInput>|null $orderTransactions
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\RefundDutyInput>|null $refundDuties
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\RefundShippingInput|null $refundShipping
     */
    public static function make(
        $returnId,
        $returnRefundLineItems,
        $applyDeductions = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $note = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $notifyCustomer = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $orderTransactions = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $refundDuties = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $refundShipping = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($returnId !== self::UNDEFINED) {
            $instance->returnId = $returnId;
        }
        if ($returnRefundLineItems !== self::UNDEFINED) {
            $instance->returnRefundLineItems = $returnRefundLineItems;
        }
        if ($applyDeductions !== self::UNDEFINED) {
            $instance->applyDeductions = $applyDeductions;
        }
        if ($note !== self::UNDEFINED) {
            $instance->note = $note;
        }
        if ($notifyCustomer !== self::UNDEFINED) {
            $instance->notifyCustomer = $notifyCustomer;
        }
        if ($orderTransactions !== self::UNDEFINED) {
            $instance->orderTransactions = $orderTransactions;
        }
        if ($refundDuties !== self::UNDEFINED) {
            $instance->refundDuties = $refundDuties;
        }
        if ($refundShipping !== self::UNDEFINED) {
            $instance->refundShipping = $refundShipping;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'returnId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'returnRefundLineItems' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ReturnRefundLineItemInput))),
            'applyDeductions' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'note' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'notifyCustomer' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'orderTransactions' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ReturnRefundOrderTransactionInput))),
            'refundDuties' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\RefundDutyInput))),
            'refundShipping' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\RefundShippingInput),
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
