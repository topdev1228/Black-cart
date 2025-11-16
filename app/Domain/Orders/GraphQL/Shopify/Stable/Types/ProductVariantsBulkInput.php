<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string|null $barcode
 * @property mixed|null $compareAtPrice
 * @property int|string|null $id
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\InventoryItemInput|null $inventoryItem
 * @property string|null $inventoryPolicy
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\InventoryLevelInput>|null $inventoryQuantities
 * @property int|string|null $mediaId
 * @property array<string>|null $mediaSrc
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetafieldInput>|null $metafields
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\VariantOptionValueInput>|null $optionValues
 * @property mixed|null $price
 * @property string|null $sku
 * @property string|null $taxCode
 * @property bool|null $taxable
 */
class ProductVariantsBulkInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string|null $barcode
     * @param mixed|null $compareAtPrice
     * @param int|string|null $id
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\InventoryItemInput|null $inventoryItem
     * @param string|null $inventoryPolicy
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\InventoryLevelInput>|null $inventoryQuantities
     * @param int|string|null $mediaId
     * @param array<string>|null $mediaSrc
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetafieldInput>|null $metafields
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\VariantOptionValueInput>|null $optionValues
     * @param mixed|null $price
     * @param string|null $sku
     * @param string|null $taxCode
     * @param bool|null $taxable
     */
    public static function make(
        $barcode = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $compareAtPrice = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $id = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $inventoryItem = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $inventoryPolicy = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $inventoryQuantities = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $mediaId = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $mediaSrc = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $metafields = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $optionValues = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $price = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $sku = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $taxCode = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $taxable = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($barcode !== self::UNDEFINED) {
            $instance->barcode = $barcode;
        }
        if ($compareAtPrice !== self::UNDEFINED) {
            $instance->compareAtPrice = $compareAtPrice;
        }
        if ($id !== self::UNDEFINED) {
            $instance->id = $id;
        }
        if ($inventoryItem !== self::UNDEFINED) {
            $instance->inventoryItem = $inventoryItem;
        }
        if ($inventoryPolicy !== self::UNDEFINED) {
            $instance->inventoryPolicy = $inventoryPolicy;
        }
        if ($inventoryQuantities !== self::UNDEFINED) {
            $instance->inventoryQuantities = $inventoryQuantities;
        }
        if ($mediaId !== self::UNDEFINED) {
            $instance->mediaId = $mediaId;
        }
        if ($mediaSrc !== self::UNDEFINED) {
            $instance->mediaSrc = $mediaSrc;
        }
        if ($metafields !== self::UNDEFINED) {
            $instance->metafields = $metafields;
        }
        if ($optionValues !== self::UNDEFINED) {
            $instance->optionValues = $optionValues;
        }
        if ($price !== self::UNDEFINED) {
            $instance->price = $price;
        }
        if ($sku !== self::UNDEFINED) {
            $instance->sku = $sku;
        }
        if ($taxCode !== self::UNDEFINED) {
            $instance->taxCode = $taxCode;
        }
        if ($taxable !== self::UNDEFINED) {
            $instance->taxable = $taxable;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'barcode' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'compareAtPrice' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'id' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'inventoryItem' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\InventoryItemInput),
            'inventoryPolicy' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'inventoryQuantities' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\InventoryLevelInput))),
            'mediaId' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'mediaSrc' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter))),
            'metafields' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetafieldInput))),
            'optionValues' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\VariantOptionValueInput))),
            'price' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'sku' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'taxCode' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'taxable' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
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
