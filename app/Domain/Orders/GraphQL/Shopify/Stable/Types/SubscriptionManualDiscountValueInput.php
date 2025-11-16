<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionManualDiscountFixedAmountInput|null $fixedAmount
 * @property int|null $percentage
 */
class SubscriptionManualDiscountValueInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionManualDiscountFixedAmountInput|null $fixedAmount
     * @param int|null $percentage
     */
    public static function make(
        $fixedAmount = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $percentage = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($fixedAmount !== self::UNDEFINED) {
            $instance->fixedAmount = $fixedAmount;
        }
        if ($percentage !== self::UNDEFINED) {
            $instance->percentage = $percentage;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'fixedAmount' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionManualDiscountFixedAmountInput),
            'percentage' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
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
