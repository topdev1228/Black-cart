<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\SellingPlanAnchorInput>|null $anchors
 * @property string|null $interval
 * @property int|null $intervalCount
 * @property int|null $maxCycles
 * @property int|null $minCycles
 */
class SellingPlanRecurringBillingPolicyInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\SellingPlanAnchorInput>|null $anchors
     * @param string|null $interval
     * @param int|null $intervalCount
     * @param int|null $maxCycles
     * @param int|null $minCycles
     */
    public static function make(
        $anchors = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $interval = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $intervalCount = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $maxCycles = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $minCycles = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($anchors !== self::UNDEFINED) {
            $instance->anchors = $anchors;
        }
        if ($interval !== self::UNDEFINED) {
            $instance->interval = $interval;
        }
        if ($intervalCount !== self::UNDEFINED) {
            $instance->intervalCount = $intervalCount;
        }
        if ($maxCycles !== self::UNDEFINED) {
            $instance->maxCycles = $maxCycles;
        }
        if ($minCycles !== self::UNDEFINED) {
            $instance->minCycles = $minCycles;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'anchors' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SellingPlanAnchorInput))),
            'interval' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'intervalCount' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'maxCycles' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'minCycles' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
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
