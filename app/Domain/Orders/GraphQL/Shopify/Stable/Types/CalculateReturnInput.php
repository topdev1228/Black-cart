<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string $orderId
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\CalculateExchangeLineItemInput>|null $exchangeLineItems
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\CalculateReturnLineItemInput>|null $returnLineItems
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ReturnShippingFeeInput|null $returnShippingFee
 */
class CalculateReturnInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string $orderId
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\CalculateExchangeLineItemInput>|null $exchangeLineItems
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\CalculateReturnLineItemInput>|null $returnLineItems
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ReturnShippingFeeInput|null $returnShippingFee
     */
    public static function make(
        $orderId,
        $exchangeLineItems = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $returnLineItems = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $returnShippingFee = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($orderId !== self::UNDEFINED) {
            $instance->orderId = $orderId;
        }
        if ($exchangeLineItems !== self::UNDEFINED) {
            $instance->exchangeLineItems = $exchangeLineItems;
        }
        if ($returnLineItems !== self::UNDEFINED) {
            $instance->returnLineItems = $returnLineItems;
        }
        if ($returnShippingFee !== self::UNDEFINED) {
            $instance->returnShippingFee = $returnShippingFee;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'orderId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'exchangeLineItems' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CalculateExchangeLineItemInput))),
            'returnLineItems' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CalculateReturnLineItemInput))),
            'returnShippingFee' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ReturnShippingFeeInput),
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
