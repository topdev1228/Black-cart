<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property mixed|null $amount
 * @property mixed|null $quantity
 */
class DiscountCustomerBuysValueInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param mixed|null $amount
     * @param mixed|null $quantity
     */
    public static function make(
        $amount = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $quantity = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($amount !== self::UNDEFINED) {
            $instance->amount = $amount;
        }
        if ($quantity !== self::UNDEFINED) {
            $instance->quantity = $quantity;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'amount' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'quantity' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
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
