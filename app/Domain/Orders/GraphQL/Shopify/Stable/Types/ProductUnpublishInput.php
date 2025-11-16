<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string $id
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\ProductPublicationInput> $productPublications
 */
class ProductUnpublishInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string $id
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\ProductPublicationInput> $productPublications
     */
    public static function make($id, $productPublications): self
    {
        $instance = new self;

        if ($id !== self::UNDEFINED) {
            $instance->id = $id;
        }
        if ($productPublications !== self::UNDEFINED) {
            $instance->productPublications = $productPublications;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'id' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'productPublications' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ProductPublicationInput))),
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
