<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string $productTaxonomyNodeId
 */
class ProductCategoryInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string $productTaxonomyNodeId
     */
    public static function make($productTaxonomyNodeId): self
    {
        $instance = new self;

        if ($productTaxonomyNodeId !== self::UNDEFINED) {
            $instance->productTaxonomyNodeId = $productTaxonomyNodeId;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'productTaxonomyNodeId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
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
