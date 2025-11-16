<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceListPriceInput> $pricesToAdd
 * @property array<int|string> $pricesToDeleteByVariantId
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\QuantityPriceBreakInput> $quantityPriceBreaksToAdd
 * @property array<int|string> $quantityPriceBreaksToDelete
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\QuantityRuleInput> $quantityRulesToAdd
 * @property array<int|string> $quantityRulesToDeleteByVariantId
 */
class QuantityPricingByVariantUpdateInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceListPriceInput> $pricesToAdd
     * @param array<int|string> $pricesToDeleteByVariantId
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\QuantityPriceBreakInput> $quantityPriceBreaksToAdd
     * @param array<int|string> $quantityPriceBreaksToDelete
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\QuantityRuleInput> $quantityRulesToAdd
     * @param array<int|string> $quantityRulesToDeleteByVariantId
     */
    public static function make(
        $pricesToAdd,
        $pricesToDeleteByVariantId,
        $quantityPriceBreaksToAdd,
        $quantityPriceBreaksToDelete,
        $quantityRulesToAdd,
        $quantityRulesToDeleteByVariantId,
    ): self {
        $instance = new self;

        if ($pricesToAdd !== self::UNDEFINED) {
            $instance->pricesToAdd = $pricesToAdd;
        }
        if ($pricesToDeleteByVariantId !== self::UNDEFINED) {
            $instance->pricesToDeleteByVariantId = $pricesToDeleteByVariantId;
        }
        if ($quantityPriceBreaksToAdd !== self::UNDEFINED) {
            $instance->quantityPriceBreaksToAdd = $quantityPriceBreaksToAdd;
        }
        if ($quantityPriceBreaksToDelete !== self::UNDEFINED) {
            $instance->quantityPriceBreaksToDelete = $quantityPriceBreaksToDelete;
        }
        if ($quantityRulesToAdd !== self::UNDEFINED) {
            $instance->quantityRulesToAdd = $quantityRulesToAdd;
        }
        if ($quantityRulesToDeleteByVariantId !== self::UNDEFINED) {
            $instance->quantityRulesToDeleteByVariantId = $quantityRulesToDeleteByVariantId;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'pricesToAdd' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceListPriceInput))),
            'pricesToDeleteByVariantId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter))),
            'quantityPriceBreaksToAdd' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\QuantityPriceBreakInput))),
            'quantityPriceBreaksToDelete' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter))),
            'quantityRulesToAdd' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\QuantityRuleInput))),
            'quantityRulesToDeleteByVariantId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter))),
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
