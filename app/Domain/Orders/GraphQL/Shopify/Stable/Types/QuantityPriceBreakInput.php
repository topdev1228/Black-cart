<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int $minimumQuantity
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MoneyInput $price
 * @property int|string $variantId
 */
class QuantityPriceBreakInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int $minimumQuantity
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MoneyInput $price
     * @param int|string $variantId
     */
    public static function make($minimumQuantity, $price, $variantId): self
    {
        $instance = new self;

        if ($minimumQuantity !== self::UNDEFINED) {
            $instance->minimumQuantity = $minimumQuantity;
        }
        if ($price !== self::UNDEFINED) {
            $instance->price = $price;
        }
        if ($variantId !== self::UNDEFINED) {
            $instance->variantId = $variantId;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'minimumQuantity' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'price' => new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MoneyInput),
            'variantId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
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
