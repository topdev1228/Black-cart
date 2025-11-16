<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\SellingPlanAnchorInput>|null $anchors
 * @property int|null $cutoff
 * @property mixed|null $fulfillmentExactTime
 * @property string|null $fulfillmentTrigger
 * @property string|null $intent
 * @property string|null $preAnchorBehavior
 */
class SellingPlanFixedDeliveryPolicyInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\SellingPlanAnchorInput>|null $anchors
     * @param int|null $cutoff
     * @param mixed|null $fulfillmentExactTime
     * @param string|null $fulfillmentTrigger
     * @param string|null $intent
     * @param string|null $preAnchorBehavior
     */
    public static function make(
        $anchors = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $cutoff = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $fulfillmentExactTime = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $fulfillmentTrigger = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $intent = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $preAnchorBehavior = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($anchors !== self::UNDEFINED) {
            $instance->anchors = $anchors;
        }
        if ($cutoff !== self::UNDEFINED) {
            $instance->cutoff = $cutoff;
        }
        if ($fulfillmentExactTime !== self::UNDEFINED) {
            $instance->fulfillmentExactTime = $fulfillmentExactTime;
        }
        if ($fulfillmentTrigger !== self::UNDEFINED) {
            $instance->fulfillmentTrigger = $fulfillmentTrigger;
        }
        if ($intent !== self::UNDEFINED) {
            $instance->intent = $intent;
        }
        if ($preAnchorBehavior !== self::UNDEFINED) {
            $instance->preAnchorBehavior = $preAnchorBehavior;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'anchors' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SellingPlanAnchorInput))),
            'cutoff' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'fulfillmentExactTime' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'fulfillmentTrigger' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'intent' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'preAnchorBehavior' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
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
