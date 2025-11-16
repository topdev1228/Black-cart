<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SellingPlanBillingPolicyInput|null $billingPolicy
 * @property string|null $category
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SellingPlanDeliveryPolicyInput|null $deliveryPolicy
 * @property string|null $description
 * @property int|string|null $id
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SellingPlanInventoryPolicyInput|null $inventoryPolicy
 * @property string|null $name
 * @property array<string>|null $options
 * @property int|null $position
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\SellingPlanPricingPolicyInput>|null $pricingPolicies
 */
class SellingPlanInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SellingPlanBillingPolicyInput|null $billingPolicy
     * @param string|null $category
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SellingPlanDeliveryPolicyInput|null $deliveryPolicy
     * @param string|null $description
     * @param int|string|null $id
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SellingPlanInventoryPolicyInput|null $inventoryPolicy
     * @param string|null $name
     * @param array<string>|null $options
     * @param int|null $position
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\SellingPlanPricingPolicyInput>|null $pricingPolicies
     */
    public static function make(
        $billingPolicy = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $category = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $deliveryPolicy = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $description = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $id = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $inventoryPolicy = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $name = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $options = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $position = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $pricingPolicies = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($billingPolicy !== self::UNDEFINED) {
            $instance->billingPolicy = $billingPolicy;
        }
        if ($category !== self::UNDEFINED) {
            $instance->category = $category;
        }
        if ($deliveryPolicy !== self::UNDEFINED) {
            $instance->deliveryPolicy = $deliveryPolicy;
        }
        if ($description !== self::UNDEFINED) {
            $instance->description = $description;
        }
        if ($id !== self::UNDEFINED) {
            $instance->id = $id;
        }
        if ($inventoryPolicy !== self::UNDEFINED) {
            $instance->inventoryPolicy = $inventoryPolicy;
        }
        if ($name !== self::UNDEFINED) {
            $instance->name = $name;
        }
        if ($options !== self::UNDEFINED) {
            $instance->options = $options;
        }
        if ($position !== self::UNDEFINED) {
            $instance->position = $position;
        }
        if ($pricingPolicies !== self::UNDEFINED) {
            $instance->pricingPolicies = $pricingPolicies;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'billingPolicy' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SellingPlanBillingPolicyInput),
            'category' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'deliveryPolicy' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SellingPlanDeliveryPolicyInput),
            'description' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'id' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'inventoryPolicy' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SellingPlanInventoryPolicyInput),
            'name' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'options' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter))),
            'position' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'pricingPolicies' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SellingPlanPricingPolicyInput))),
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
