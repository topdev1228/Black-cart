<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int $afterCycle
 * @property string|null $adjustmentType
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SellingPlanPricingPolicyValueInput|null $adjustmentValue
 * @property int|string|null $id
 */
class SellingPlanRecurringPricingPolicyInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int $afterCycle
     * @param string|null $adjustmentType
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SellingPlanPricingPolicyValueInput|null $adjustmentValue
     * @param int|string|null $id
     */
    public static function make(
        $afterCycle,
        $adjustmentType = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $adjustmentValue = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $id = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($afterCycle !== self::UNDEFINED) {
            $instance->afterCycle = $afterCycle;
        }
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
            'afterCycle' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IntConverter),
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
