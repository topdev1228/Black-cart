<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\VariantOptionValueInput> $optionValues
 * @property string|null $barcode
 * @property mixed|null $compareAtPrice
 * @property string|null $harmonizedSystemCode
 * @property int|string|null $id
 * @property string|null $inventoryPolicy
 * @property int|string|null $mediaId
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetafieldInput>|null $metafields
 * @property int|null $position
 * @property mixed|null $price
 * @property bool|null $requiresComponents
 * @property bool|null $requiresShipping
 * @property string|null $sku
 * @property string|null $taxCode
 * @property bool|null $taxable
 * @property float|int|null $weight
 * @property string|null $weightUnit
 */
class ProductVariantSetInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\VariantOptionValueInput> $optionValues
     * @param string|null $barcode
     * @param mixed|null $compareAtPrice
     * @param string|null $harmonizedSystemCode
     * @param int|string|null $id
     * @param string|null $inventoryPolicy
     * @param int|string|null $mediaId
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetafieldInput>|null $metafields
     * @param int|null $position
     * @param mixed|null $price
     * @param bool|null $requiresComponents
     * @param bool|null $requiresShipping
     * @param string|null $sku
     * @param string|null $taxCode
     * @param bool|null $taxable
     * @param float|int|null $weight
     * @param string|null $weightUnit
     */
    public static function make(
        $optionValues,
        $barcode = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $compareAtPrice = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $harmonizedSystemCode = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $id = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $inventoryPolicy = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $mediaId = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $metafields = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $position = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $price = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $requiresComponents = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $requiresShipping = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $sku = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $taxCode = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $taxable = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $weight = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $weightUnit = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($optionValues !== self::UNDEFINED) {
            $instance->optionValues = $optionValues;
        }
        if ($barcode !== self::UNDEFINED) {
            $instance->barcode = $barcode;
        }
        if ($compareAtPrice !== self::UNDEFINED) {
            $instance->compareAtPrice = $compareAtPrice;
        }
        if ($harmonizedSystemCode !== self::UNDEFINED) {
            $instance->harmonizedSystemCode = $harmonizedSystemCode;
        }
        if ($id !== self::UNDEFINED) {
            $instance->id = $id;
        }
        if ($inventoryPolicy !== self::UNDEFINED) {
            $instance->inventoryPolicy = $inventoryPolicy;
        }
        if ($mediaId !== self::UNDEFINED) {
            $instance->mediaId = $mediaId;
        }
        if ($metafields !== self::UNDEFINED) {
            $instance->metafields = $metafields;
        }
        if ($position !== self::UNDEFINED) {
            $instance->position = $position;
        }
        if ($price !== self::UNDEFINED) {
            $instance->price = $price;
        }
        if ($requiresComponents !== self::UNDEFINED) {
            $instance->requiresComponents = $requiresComponents;
        }
        if ($requiresShipping !== self::UNDEFINED) {
            $instance->requiresShipping = $requiresShipping;
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
        if ($weight !== self::UNDEFINED) {
            $instance->weight = $weight;
        }
        if ($weightUnit !== self::UNDEFINED) {
            $instance->weightUnit = $weightUnit;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'optionValues' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\VariantOptionValueInput))),
            'barcode' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'compareAtPrice' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'harmonizedSystemCode' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'id' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'inventoryPolicy' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'mediaId' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'metafields' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetafieldInput))),
            'position' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'price' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'requiresComponents' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'requiresShipping' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'sku' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'taxCode' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'taxable' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'weight' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\FloatConverter),
            'weightUnit' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
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
