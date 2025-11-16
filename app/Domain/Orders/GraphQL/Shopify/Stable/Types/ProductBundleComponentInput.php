<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\ProductBundleComponentOptionSelectionInput> $optionSelections
 * @property int|string $productId
 * @property int|null $quantity
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ProductBundleComponentQuantityOptionInput|null $quantityOption
 */
class ProductBundleComponentInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\ProductBundleComponentOptionSelectionInput> $optionSelections
     * @param int|string $productId
     * @param int|null $quantity
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ProductBundleComponentQuantityOptionInput|null $quantityOption
     */
    public static function make(
        $optionSelections,
        $productId,
        $quantity = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $quantityOption = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($optionSelections !== self::UNDEFINED) {
            $instance->optionSelections = $optionSelections;
        }
        if ($productId !== self::UNDEFINED) {
            $instance->productId = $productId;
        }
        if ($quantity !== self::UNDEFINED) {
            $instance->quantity = $quantity;
        }
        if ($quantityOption !== self::UNDEFINED) {
            $instance->quantityOption = $quantityOption;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'optionSelections' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ProductBundleComponentOptionSelectionInput))),
            'productId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'quantity' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'quantityOption' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ProductBundleComponentQuantityOptionInput),
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
