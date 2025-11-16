<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string|null $parentProductId
 * @property int|string|null $parentProductVariantId
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceInput|null $priceInput
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\ProductVariantGroupRelationshipInput>|null $productVariantRelationshipsToCreate
 * @property array<int|string>|null $productVariantRelationshipsToRemove
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\ProductVariantGroupRelationshipInput>|null $productVariantRelationshipsToUpdate
 * @property bool|null $removeAllProductVariantRelationships
 */
class ProductVariantRelationshipUpdateInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string|null $parentProductId
     * @param int|string|null $parentProductVariantId
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceInput|null $priceInput
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\ProductVariantGroupRelationshipInput>|null $productVariantRelationshipsToCreate
     * @param array<int|string>|null $productVariantRelationshipsToRemove
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\ProductVariantGroupRelationshipInput>|null $productVariantRelationshipsToUpdate
     * @param bool|null $removeAllProductVariantRelationships
     */
    public static function make(
        $parentProductId = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $parentProductVariantId = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $priceInput = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $productVariantRelationshipsToCreate = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $productVariantRelationshipsToRemove = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $productVariantRelationshipsToUpdate = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $removeAllProductVariantRelationships = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($parentProductId !== self::UNDEFINED) {
            $instance->parentProductId = $parentProductId;
        }
        if ($parentProductVariantId !== self::UNDEFINED) {
            $instance->parentProductVariantId = $parentProductVariantId;
        }
        if ($priceInput !== self::UNDEFINED) {
            $instance->priceInput = $priceInput;
        }
        if ($productVariantRelationshipsToCreate !== self::UNDEFINED) {
            $instance->productVariantRelationshipsToCreate = $productVariantRelationshipsToCreate;
        }
        if ($productVariantRelationshipsToRemove !== self::UNDEFINED) {
            $instance->productVariantRelationshipsToRemove = $productVariantRelationshipsToRemove;
        }
        if ($productVariantRelationshipsToUpdate !== self::UNDEFINED) {
            $instance->productVariantRelationshipsToUpdate = $productVariantRelationshipsToUpdate;
        }
        if ($removeAllProductVariantRelationships !== self::UNDEFINED) {
            $instance->removeAllProductVariantRelationships = $removeAllProductVariantRelationships;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'parentProductId' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'parentProductVariantId' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'priceInput' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceInput),
            'productVariantRelationshipsToCreate' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ProductVariantGroupRelationshipInput))),
            'productVariantRelationshipsToRemove' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter))),
            'productVariantRelationshipsToUpdate' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ProductVariantGroupRelationshipInput))),
            'removeAllProductVariantRelationships' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
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
