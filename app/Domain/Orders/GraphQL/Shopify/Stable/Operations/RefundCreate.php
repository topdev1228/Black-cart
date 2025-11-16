<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Operations;

/**
 * @extends \Spawnia\Sailor\Operation<\App\Domain\Orders\GraphQL\Shopify\Stable\Operations\RefundCreate\RefundCreateResult>
 */
class RefundCreate extends \Spawnia\Sailor\Operation
{
    /**
     * @param int|string $orderId
     * @param string $note
     * @param mixed $amount
     * @param string $gateway
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\RefundLineItemInput>|null $refundLineItems
     * @param string|null $currency
     * @param int|string|null $parentTransactionId
     */
    public static function execute(
        $orderId,
        $note,
        $amount,
        $gateway,
        $refundLineItems = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $currency = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $parentTransactionId = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): RefundCreate\RefundCreateResult {
        return self::executeOperation(
            $orderId,
            $note,
            $amount,
            $gateway,
            $refundLineItems,
            $currency,
            $parentTransactionId,
        );
    }

    protected static function converters(): array
    {
        static $converters;

        return $converters ??= [
            ['orderId', new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter)],
            ['note', new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter)],
            ['amount', new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ScalarConverter)],
            ['gateway', new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter)],
            ['refundLineItems', new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\RefundLineItemInput)))],
            ['currency', new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter)],
            ['parentTransactionId', new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter)],
        ];
    }

    public static function document(): string
    {
        return /* @lang GraphQL */ 'mutation RefundCreate($orderId: ID!, $note: String!, $refundLineItems: [RefundLineItemInput!], $currency: CurrencyCode, $amount: Money!, $gateway: String!, $parentTransactionId: ID) {
          __typename
          refundCreate(
            input: { orderId: $orderId, note: $note, refundLineItems: $refundLineItems, currency: $currency, transactions: [{ orderId: $orderId, kind: REFUND, amount: $amount, gateway: $gateway, parentId: $parentTransactionId }] }
          ) {
            __typename
            userErrors {
              __typename
              field
              message
            }
            refund {
              __typename
              id
            }
          }
        }';
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
