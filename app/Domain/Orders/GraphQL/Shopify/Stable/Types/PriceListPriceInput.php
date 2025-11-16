<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MoneyInput $price
 * @property int|string $variantId
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MoneyInput|null $compareAtPrice
 */
class PriceListPriceInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MoneyInput $price
     * @param int|string $variantId
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MoneyInput|null $compareAtPrice
     */
    public static function make(
        $price,
        $variantId,
        $compareAtPrice = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($price !== self::UNDEFINED) {
            $instance->price = $price;
        }
        if ($variantId !== self::UNDEFINED) {
            $instance->variantId = $variantId;
        }
        if ($compareAtPrice !== self::UNDEFINED) {
            $instance->compareAtPrice = $compareAtPrice;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'price' => new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MoneyInput),
            'variantId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'compareAtPrice' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MoneyInput),
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
