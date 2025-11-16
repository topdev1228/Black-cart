<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string $contractId
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionBillingCycleSelector $selector
 */
class SubscriptionBillingCycleInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string $contractId
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionBillingCycleSelector $selector
     */
    public static function make($contractId, $selector): self
    {
        $instance = new self;

        if ($contractId !== self::UNDEFINED) {
            $instance->contractId = $contractId;
        }
        if ($selector !== self::UNDEFINED) {
            $instance->selector = $selector;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'contractId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'selector' => new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionBillingCycleSelector),
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
