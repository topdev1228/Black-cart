<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property array<int|string>|null $productVariantsToAdd
 * @property array<int|string>|null $productVariantsToRemove
 * @property array<int|string>|null $productsToAdd
 * @property array<int|string>|null $productsToRemove
 */
class DiscountProductsInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param array<int|string>|null $productVariantsToAdd
     * @param array<int|string>|null $productVariantsToRemove
     * @param array<int|string>|null $productsToAdd
     * @param array<int|string>|null $productsToRemove
     */
    public static function make(
        $productVariantsToAdd = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $productVariantsToRemove = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $productsToAdd = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $productsToRemove = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($productVariantsToAdd !== self::UNDEFINED) {
            $instance->productVariantsToAdd = $productVariantsToAdd;
        }
        if ($productVariantsToRemove !== self::UNDEFINED) {
            $instance->productVariantsToRemove = $productVariantsToRemove;
        }
        if ($productsToAdd !== self::UNDEFINED) {
            $instance->productsToAdd = $productsToAdd;
        }
        if ($productsToRemove !== self::UNDEFINED) {
            $instance->productsToRemove = $productsToRemove;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'productVariantsToAdd' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter))),
            'productVariantsToRemove' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter))),
            'productsToAdd' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter))),
            'productsToRemove' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter))),
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
