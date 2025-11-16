<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string|null $adjustmentType
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SellingPlanPricingPolicyValueInput|null $adjustmentValue
 * @property int|string|null $id
 */
class SellingPlanFixedPricingPolicyInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string|null $adjustmentType
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SellingPlanPricingPolicyValueInput|null $adjustmentValue
     * @param int|string|null $id
     */
    public static function make(
        $adjustmentType = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $adjustmentValue = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $id = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($adjustmentType !== self::UNDEFINED) {
            $instance->adjustmentType = $adjustmentType;
        }
        if ($adjustmentValue !== self::UNDEFINED) {
            $instance->adjustmentValue = $adjustmentValue;
        }
        if ($id !== self::UNDEFINED) {
            $instance->id = $id;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'adjustmentType' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'adjustmentValue' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SellingPlanPricingPolicyValueInput),
            'id' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
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
