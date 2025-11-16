<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property mixed|null $amount
 * @property bool|null $fullRefund
 */
class ShippingRefundInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param mixed|null $amount
     * @param bool|null $fullRefund
     */
    public static function make(
        $amount = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $fullRefund = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($amount !== self::UNDEFINED) {
            $instance->amount = $amount;
        }
        if ($fullRefund !== self::UNDEFINED) {
            $instance->fullRefund = $fullRefund;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'amount' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'fullRefund' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
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
