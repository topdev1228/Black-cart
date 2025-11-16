<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string $orderId
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\ReturnLineItemInput> $returnLineItems
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\ExchangeLineItemInput>|null $exchangeLineItems
 * @property bool|null $notifyCustomer
 * @property mixed|null $requestedAt
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ReturnShippingFeeInput|null $returnShippingFee
 */
class ReturnInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string $orderId
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\ReturnLineItemInput> $returnLineItems
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\ExchangeLineItemInput>|null $exchangeLineItems
     * @param bool|null $notifyCustomer
     * @param mixed|null $requestedAt
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ReturnShippingFeeInput|null $returnShippingFee
     */
    public static function make(
        $orderId,
        $returnLineItems,
        $exchangeLineItems = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $notifyCustomer = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $requestedAt = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $returnShippingFee = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($orderId !== self::UNDEFINED) {
            $instance->orderId = $orderId;
        }
        if ($returnLineItems !== self::UNDEFINED) {
            $instance->returnLineItems = $returnLineItems;
        }
        if ($exchangeLineItems !== self::UNDEFINED) {
            $instance->exchangeLineItems = $exchangeLineItems;
        }
        if ($notifyCustomer !== self::UNDEFINED) {
            $instance->notifyCustomer = $notifyCustomer;
        }
        if ($requestedAt !== self::UNDEFINED) {
            $instance->requestedAt = $requestedAt;
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
            'returnLineItems' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ReturnLineItemInput))),
            'exchangeLineItems' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ExchangeLineItemInput))),
            'notifyCustomer' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'requestedAt' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
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
