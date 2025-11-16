<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property mixed $amount
 * @property string $gateway
 * @property string $kind
 * @property int|string $orderId
 * @property int|string|null $parentId
 */
class OrderTransactionInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param mixed $amount
     * @param string $gateway
     * @param string $kind
     * @param int|string $orderId
     * @param int|string|null $parentId
     */
    public static function make(
        $amount,
        $gateway,
        $kind,
        $orderId,
        $parentId = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($amount !== self::UNDEFINED) {
            $instance->amount = $amount;
        }
        if ($gateway !== self::UNDEFINED) {
            $instance->gateway = $gateway;
        }
        if ($kind !== self::UNDEFINED) {
            $instance->kind = $kind;
        }
        if ($orderId !== self::UNDEFINED) {
            $instance->orderId = $orderId;
        }
        if ($parentId !== self::UNDEFINED) {
            $instance->parentId = $parentId;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'amount' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'gateway' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'kind' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'orderId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'parentId' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
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
