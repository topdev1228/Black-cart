<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property bool|null $appliesOnOneTimePurchase
 * @property bool|null $appliesOnSubscription
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountItemsInput|null $items
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountCustomerGetsValueInput|null $value
 */
class DiscountCustomerGetsInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param bool|null $appliesOnOneTimePurchase
     * @param bool|null $appliesOnSubscription
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountItemsInput|null $items
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountCustomerGetsValueInput|null $value
     */
    public static function make(
        $appliesOnOneTimePurchase = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $appliesOnSubscription = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $items = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $value = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($appliesOnOneTimePurchase !== self::UNDEFINED) {
            $instance->appliesOnOneTimePurchase = $appliesOnOneTimePurchase;
        }
        if ($appliesOnSubscription !== self::UNDEFINED) {
            $instance->appliesOnSubscription = $appliesOnSubscription;
        }
        if ($items !== self::UNDEFINED) {
            $instance->items = $items;
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
            'appliesOnOneTimePurchase' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'appliesOnSubscription' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'items' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountItemsInput),
            'value' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountCustomerGetsValueInput),
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
