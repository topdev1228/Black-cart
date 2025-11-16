<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property bool|null $all
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountCollectionsInput|null $collections
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountProductsInput|null $products
 */
class DiscountItemsInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param bool|null $all
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountCollectionsInput|null $collections
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountProductsInput|null $products
     */
    public static function make(
        $all = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $collections = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $products = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($all !== self::UNDEFINED) {
            $instance->all = $all;
        }
        if ($collections !== self::UNDEFINED) {
            $instance->collections = $collections;
        }
        if ($products !== self::UNDEFINED) {
            $instance->products = $products;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'all' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'collections' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountCollectionsInput),
            'products' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountProductsInput),
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
