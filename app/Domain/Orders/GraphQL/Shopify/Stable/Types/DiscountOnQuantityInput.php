<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountEffectInput|null $effect
 * @property mixed|null $quantity
 */
class DiscountOnQuantityInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountEffectInput|null $effect
     * @param mixed|null $quantity
     */
    public static function make(
        $effect = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $quantity = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($effect !== self::UNDEFINED) {
            $instance->effect = $effect;
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
            'effect' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountEffectInput),
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
