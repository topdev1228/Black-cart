<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int $quantity
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DraftOrderAppliedDiscountInput|null $appliedDiscount
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\AttributeInput>|null $customAttributes
 * @property mixed|null $originalUnitPrice
 * @property bool|null $requiresShipping
 * @property string|null $sku
 * @property bool|null $taxable
 * @property string|null $title
 * @property string|null $uuid
 * @property int|string|null $variantId
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\WeightInput|null $weight
 */
class DraftOrderLineItemInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int $quantity
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DraftOrderAppliedDiscountInput|null $appliedDiscount
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\AttributeInput>|null $customAttributes
     * @param mixed|null $originalUnitPrice
     * @param bool|null $requiresShipping
     * @param string|null $sku
     * @param bool|null $taxable
     * @param string|null $title
     * @param string|null $uuid
     * @param int|string|null $variantId
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\WeightInput|null $weight
     */
    public static function make(
        $quantity,
        $appliedDiscount = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $customAttributes = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $originalUnitPrice = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $requiresShipping = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $sku = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $taxable = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $title = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $uuid = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $variantId = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $weight = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($quantity !== self::UNDEFINED) {
            $instance->quantity = $quantity;
        }
        if ($appliedDiscount !== self::UNDEFINED) {
            $instance->appliedDiscount = $appliedDiscount;
        }
        if ($customAttributes !== self::UNDEFINED) {
            $instance->customAttributes = $customAttributes;
        }
        if ($originalUnitPrice !== self::UNDEFINED) {
            $instance->originalUnitPrice = $originalUnitPrice;
        }
        if ($requiresShipping !== self::UNDEFINED) {
            $instance->requiresShipping = $requiresShipping;
        }
        if ($sku !== self::UNDEFINED) {
            $instance->sku = $sku;
        }
        if ($taxable !== self::UNDEFINED) {
            $instance->taxable = $taxable;
        }
        if ($title !== self::UNDEFINED) {
            $instance->title = $title;
        }
        if ($uuid !== self::UNDEFINED) {
            $instance->uuid = $uuid;
        }
        if ($variantId !== self::UNDEFINED) {
            $instance->variantId = $variantId;
        }
        if ($weight !== self::UNDEFINED) {
            $instance->weight = $weight;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'quantity' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'appliedDiscount' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DraftOrderAppliedDiscountInput),
            'customAttributes' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\AttributeInput))),
            'originalUnitPrice' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'requiresShipping' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'sku' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'taxable' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'title' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'uuid' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'variantId' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'weight' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\WeightInput),
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
