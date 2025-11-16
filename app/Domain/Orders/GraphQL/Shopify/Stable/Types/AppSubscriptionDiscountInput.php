<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|null $durationLimitInIntervals
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\AppSubscriptionDiscountValueInput|null $value
 */
class AppSubscriptionDiscountInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|null $durationLimitInIntervals
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\AppSubscriptionDiscountValueInput|null $value
     */
    public static function make(
        $durationLimitInIntervals = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $value = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($durationLimitInIntervals !== self::UNDEFINED) {
            $instance->durationLimitInIntervals = $durationLimitInIntervals;
        }
        if ($value !== self::UNDEFINED) {
            $instance->value = $value;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'durationLimitInIntervals' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'value' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\AppSubscriptionDiscountValueInput),
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
