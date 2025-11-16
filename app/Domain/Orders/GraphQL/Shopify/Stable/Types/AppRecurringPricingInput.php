<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MoneyInput $price
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\AppSubscriptionDiscountInput|null $discount
 * @property string|null $interval
 */
class AppRecurringPricingInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MoneyInput $price
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\AppSubscriptionDiscountInput|null $discount
     * @param string|null $interval
     */
    public static function make(
        $price,
        $discount = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $interval = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($price !== self::UNDEFINED) {
            $instance->price = $price;
        }
        if ($discount !== self::UNDEFINED) {
            $instance->discount = $discount;
        }
        if ($interval !== self::UNDEFINED) {
            $instance->interval = $interval;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'price' => new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MoneyInput),
            'discount' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\AppSubscriptionDiscountInput),
            'interval' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
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
