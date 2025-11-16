<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property mixed|null $currentPrice
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\AttributeInput>|null $customAttributes
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionPricingPolicyInput|null $pricingPolicy
 * @property int|string|null $productVariantId
 * @property int|null $quantity
 * @property int|string|null $sellingPlanId
 * @property string|null $sellingPlanName
 */
class SubscriptionLineUpdateInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param mixed|null $currentPrice
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\AttributeInput>|null $customAttributes
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionPricingPolicyInput|null $pricingPolicy
     * @param int|string|null $productVariantId
     * @param int|null $quantity
     * @param int|string|null $sellingPlanId
     * @param string|null $sellingPlanName
     */
    public static function make(
        $currentPrice = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $customAttributes = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $pricingPolicy = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $productVariantId = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $quantity = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $sellingPlanId = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $sellingPlanName = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($currentPrice !== self::UNDEFINED) {
            $instance->currentPrice = $currentPrice;
        }
        if ($customAttributes !== self::UNDEFINED) {
            $instance->customAttributes = $customAttributes;
        }
        if ($pricingPolicy !== self::UNDEFINED) {
            $instance->pricingPolicy = $pricingPolicy;
        }
        if ($productVariantId !== self::UNDEFINED) {
            $instance->productVariantId = $productVariantId;
        }
        if ($quantity !== self::UNDEFINED) {
            $instance->quantity = $quantity;
        }
        if ($sellingPlanId !== self::UNDEFINED) {
            $instance->sellingPlanId = $sellingPlanId;
        }
        if ($sellingPlanName !== self::UNDEFINED) {
            $instance->sellingPlanName = $sellingPlanName;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'currentPrice' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'customAttributes' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\AttributeInput))),
            'pricingPolicy' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionPricingPolicyInput),
            'productVariantId' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'quantity' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'sellingPlanId' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'sellingPlanName' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
