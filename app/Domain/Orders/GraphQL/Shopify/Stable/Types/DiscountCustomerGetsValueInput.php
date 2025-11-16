<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountAmountInput|null $discountAmount
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountOnQuantityInput|null $discountOnQuantity
 * @property float|int|null $percentage
 */
class DiscountCustomerGetsValueInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountAmountInput|null $discountAmount
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountOnQuantityInput|null $discountOnQuantity
     * @param float|int|null $percentage
     */
    public static function make(
        $discountAmount = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $discountOnQuantity = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $percentage = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($discountAmount !== self::UNDEFINED) {
            $instance->discountAmount = $discountAmount;
        }
        if ($discountOnQuantity !== self::UNDEFINED) {
            $instance->discountOnQuantity = $discountOnQuantity;
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
            'discountAmount' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountAmountInput),
            'discountOnQuantity' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountOnQuantityInput),
            'percentage' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\FloatConverter),
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
