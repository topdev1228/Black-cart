<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $adjustmentType
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SellingPlanPricingPolicyValueInput $adjustmentValue
 * @property int $afterCycle
 * @property mixed $computedPrice
 */
class SubscriptionPricingPolicyCycleDiscountsInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $adjustmentType
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SellingPlanPricingPolicyValueInput $adjustmentValue
     * @param int $afterCycle
     * @param mixed $computedPrice
     */
    public static function make($adjustmentType, $adjustmentValue, $afterCycle, $computedPrice): self
    {
        $instance = new self;

        if ($adjustmentType !== self::UNDEFINED) {
            $instance->adjustmentType = $adjustmentType;
        }
        if ($adjustmentValue !== self::UNDEFINED) {
            $instance->adjustmentValue = $adjustmentValue;
        }
        if ($afterCycle !== self::UNDEFINED) {
            $instance->afterCycle = $afterCycle;
        }
        if ($computedPrice !== self::UNDEFINED) {
            $instance->computedPrice = $computedPrice;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'adjustmentType' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'adjustmentValue' => new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SellingPlanPricingPolicyValueInput),
            'afterCycle' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'computedPrice' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
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
