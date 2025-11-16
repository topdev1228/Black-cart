<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property array<int|string>|null $collectionIds
 * @property array<int|string>|null $productIds
 * @property array<int|string>|null $productVariantIds
 */
class PriceRuleItemPrerequisitesInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param array<int|string>|null $collectionIds
     * @param array<int|string>|null $productIds
     * @param array<int|string>|null $productVariantIds
     */
    public static function make(
        $collectionIds = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $productIds = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $productVariantIds = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($collectionIds !== self::UNDEFINED) {
            $instance->collectionIds = $collectionIds;
        }
        if ($productIds !== self::UNDEFINED) {
            $instance->productIds = $productIds;
        }
        if ($productVariantIds !== self::UNDEFINED) {
            $instance->productVariantIds = $productVariantIds;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'collectionIds' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter))),
            'productIds' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter))),
            'productVariantIds' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter))),
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
