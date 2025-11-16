<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string $productId
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\ProductBundleComponentInput>|null $components
 * @property string|null $title
 */
class ProductBundleUpdateInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string $productId
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\ProductBundleComponentInput>|null $components
     * @param string|null $title
     */
    public static function make(
        $productId,
        $components = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $title = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($productId !== self::UNDEFINED) {
            $instance->productId = $productId;
        }
        if ($components !== self::UNDEFINED) {
            $instance->components = $components;
        }
        if ($title !== self::UNDEFINED) {
            $instance->title = $title;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'productId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'components' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ProductBundleComponentInput))),
            'title' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
