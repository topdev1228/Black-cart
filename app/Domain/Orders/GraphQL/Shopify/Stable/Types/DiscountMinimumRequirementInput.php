<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountMinimumQuantityInput|null $quantity
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountMinimumSubtotalInput|null $subtotal
 */
class DiscountMinimumRequirementInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountMinimumQuantityInput|null $quantity
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountMinimumSubtotalInput|null $subtotal
     */
    public static function make(
        $quantity = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $subtotal = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($quantity !== self::UNDEFINED) {
            $instance->quantity = $quantity;
        }
        if ($subtotal !== self::UNDEFINED) {
            $instance->subtotal = $subtotal;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'quantity' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountMinimumQuantityInput),
            'subtotal' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountMinimumSubtotalInput),
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
