<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|null $allocationLimit
 * @property string|null $allocationMethod
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountCombinesWithInput|null $combinesWith
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceRuleCustomerSelectionInput|null $customerSelection
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceRuleItemEntitlementsInput|null $itemEntitlements
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceRuleItemPrerequisitesInput|null $itemPrerequisites
 * @property bool|null $oncePerCustomer
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceRuleQuantityRangeInput|null $prerequisiteQuantityRange
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceRuleMoneyRangeInput|null $prerequisiteShippingPriceRange
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceRuleMoneyRangeInput|null $prerequisiteSubtotalRange
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceRulePrerequisiteToEntitlementQuantityRatioInput|null $prerequisiteToEntitlementQuantityRatio
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceRuleShippingEntitlementsInput|null $shippingEntitlements
 * @property string|null $target
 * @property string|null $title
 * @property int|null $usageLimit
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceRuleValidityPeriodInput|null $validityPeriod
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceRuleValueInput|null $value
 */
class PriceRuleInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|null $allocationLimit
     * @param string|null $allocationMethod
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountCombinesWithInput|null $combinesWith
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceRuleCustomerSelectionInput|null $customerSelection
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceRuleItemEntitlementsInput|null $itemEntitlements
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceRuleItemPrerequisitesInput|null $itemPrerequisites
     * @param bool|null $oncePerCustomer
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceRuleQuantityRangeInput|null $prerequisiteQuantityRange
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceRuleMoneyRangeInput|null $prerequisiteShippingPriceRange
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceRuleMoneyRangeInput|null $prerequisiteSubtotalRange
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceRulePrerequisiteToEntitlementQuantityRatioInput|null $prerequisiteToEntitlementQuantityRatio
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceRuleShippingEntitlementsInput|null $shippingEntitlements
     * @param string|null $target
     * @param string|null $title
     * @param int|null $usageLimit
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceRuleValidityPeriodInput|null $validityPeriod
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceRuleValueInput|null $value
     */
    public static function make(
        $allocationLimit = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $allocationMethod = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $combinesWith = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $customerSelection = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $itemEntitlements = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $itemPrerequisites = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $oncePerCustomer = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $prerequisiteQuantityRange = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $prerequisiteShippingPriceRange = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $prerequisiteSubtotalRange = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $prerequisiteToEntitlementQuantityRatio = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $shippingEntitlements = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $target = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $title = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $usageLimit = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $validityPeriod = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $value = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($allocationLimit !== self::UNDEFINED) {
            $instance->allocationLimit = $allocationLimit;
        }
        if ($allocationMethod !== self::UNDEFINED) {
            $instance->allocationMethod = $allocationMethod;
        }
        if ($combinesWith !== self::UNDEFINED) {
            $instance->combinesWith = $combinesWith;
        }
        if ($customerSelection !== self::UNDEFINED) {
            $instance->customerSelection = $customerSelection;
        }
        if ($itemEntitlements !== self::UNDEFINED) {
            $instance->itemEntitlements = $itemEntitlements;
        }
        if ($itemPrerequisites !== self::UNDEFINED) {
            $instance->itemPrerequisites = $itemPrerequisites;
        }
        if ($oncePerCustomer !== self::UNDEFINED) {
            $instance->oncePerCustomer = $oncePerCustomer;
        }
        if ($prerequisiteQuantityRange !== self::UNDEFINED) {
            $instance->prerequisiteQuantityRange = $prerequisiteQuantityRange;
        }
        if ($prerequisiteShippingPriceRange !== self::UNDEFINED) {
            $instance->prerequisiteShippingPriceRange = $prerequisiteShippingPriceRange;
        }
        if ($prerequisiteSubtotalRange !== self::UNDEFINED) {
            $instance->prerequisiteSubtotalRange = $prerequisiteSubtotalRange;
        }
        if ($prerequisiteToEntitlementQuantityRatio !== self::UNDEFINED) {
            $instance->prerequisiteToEntitlementQuantityRatio = $prerequisiteToEntitlementQuantityRatio;
        }
        if ($shippingEntitlements !== self::UNDEFINED) {
            $instance->shippingEntitlements = $shippingEntitlements;
        }
        if ($target !== self::UNDEFINED) {
            $instance->target = $target;
        }
        if ($title !== self::UNDEFINED) {
            $instance->title = $title;
        }
        if ($usageLimit !== self::UNDEFINED) {
            $instance->usageLimit = $usageLimit;
        }
        if ($validityPeriod !== self::UNDEFINED) {
            $instance->validityPeriod = $validityPeriod;
        }
        if ($value !== self::UNDEFINED) {
            $instance->value = $value;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'allocationLimit' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'allocationMethod' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'combinesWith' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountCombinesWithInput),
            'customerSelection' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceRuleCustomerSelectionInput),
            'itemEntitlements' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceRuleItemEntitlementsInput),
            'itemPrerequisites' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceRuleItemPrerequisitesInput),
            'oncePerCustomer' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'prerequisiteQuantityRange' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceRuleQuantityRangeInput),
            'prerequisiteShippingPriceRange' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceRuleMoneyRangeInput),
            'prerequisiteSubtotalRange' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceRuleMoneyRangeInput),
            'prerequisiteToEntitlementQuantityRatio' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceRulePrerequisiteToEntitlementQuantityRatioInput),
            'shippingEntitlements' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceRuleShippingEntitlementsInput),
            'target' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'title' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'usageLimit' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'validityPeriod' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceRuleValidityPeriodInput),
            'value' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceRuleValueInput),
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
