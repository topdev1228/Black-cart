<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $interval
 * @property int $intervalCount
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\SellingPlanAnchorInput>|null $anchors
 */
class SubscriptionDeliveryPolicyInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $interval
     * @param int $intervalCount
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\SellingPlanAnchorInput>|null $anchors
     */
    public static function make(
        $interval,
        $intervalCount,
        $anchors = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($interval !== self::UNDEFINED) {
            $instance->interval = $interval;
        }
        if ($intervalCount !== self::UNDEFINED) {
            $instance->intervalCount = $intervalCount;
        }
        if ($anchors !== self::UNDEFINED) {
            $instance->anchors = $anchors;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'interval' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'intervalCount' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'anchors' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SellingPlanAnchorInput))),
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
