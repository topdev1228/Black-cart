<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionManualDiscountEntitledLinesInput|null $entitledLines
 * @property int|null $recurringCycleLimit
 * @property string|null $title
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionManualDiscountValueInput|null $value
 */
class SubscriptionManualDiscountInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionManualDiscountEntitledLinesInput|null $entitledLines
     * @param int|null $recurringCycleLimit
     * @param string|null $title
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionManualDiscountValueInput|null $value
     */
    public static function make(
        $entitledLines = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $recurringCycleLimit = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $title = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $value = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($entitledLines !== self::UNDEFINED) {
            $instance->entitledLines = $entitledLines;
        }
        if ($recurringCycleLimit !== self::UNDEFINED) {
            $instance->recurringCycleLimit = $recurringCycleLimit;
        }
        if ($title !== self::UNDEFINED) {
            $instance->title = $title;
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
            'entitledLines' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionManualDiscountEntitledLinesInput),
            'recurringCycleLimit' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'title' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'value' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionManualDiscountValueInput),
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
