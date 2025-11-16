<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int $quantity
 * @property int|string $variantId
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ExchangeLineItemAppliedDiscountInput|null $appliedDiscount
 */
class CalculateExchangeLineItemInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int $quantity
     * @param int|string $variantId
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ExchangeLineItemAppliedDiscountInput|null $appliedDiscount
     */
    public static function make(
        $quantity,
        $variantId,
        $appliedDiscount = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($quantity !== self::UNDEFINED) {
            $instance->quantity = $quantity;
        }
        if ($variantId !== self::UNDEFINED) {
            $instance->variantId = $variantId;
        }
        if ($appliedDiscount !== self::UNDEFINED) {
            $instance->appliedDiscount = $appliedDiscount;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'quantity' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'variantId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'appliedDiscount' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ExchangeLineItemAppliedDiscountInput),
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
