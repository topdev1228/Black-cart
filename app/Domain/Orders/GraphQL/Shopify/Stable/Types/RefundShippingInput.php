<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property bool|null $fullRefund
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MoneyInput|null $shippingRefundAmount
 */
class RefundShippingInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param bool|null $fullRefund
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MoneyInput|null $shippingRefundAmount
     */
    public static function make(
        $fullRefund = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $shippingRefundAmount = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($fullRefund !== self::UNDEFINED) {
            $instance->fullRefund = $fullRefund;
        }
        if ($shippingRefundAmount !== self::UNDEFINED) {
            $instance->shippingRefundAmount = $shippingRefundAmount;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'fullRefund' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'shippingRefundAmount' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MoneyInput),
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
