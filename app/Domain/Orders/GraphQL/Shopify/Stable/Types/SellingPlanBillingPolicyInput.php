<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SellingPlanFixedBillingPolicyInput|null $fixed
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SellingPlanRecurringBillingPolicyInput|null $recurring
 */
class SellingPlanBillingPolicyInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SellingPlanFixedBillingPolicyInput|null $fixed
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SellingPlanRecurringBillingPolicyInput|null $recurring
     */
    public static function make(
        $fixed = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $recurring = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($fixed !== self::UNDEFINED) {
            $instance->fixed = $fixed;
        }
        if ($recurring !== self::UNDEFINED) {
            $instance->recurring = $recurring;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'fixed' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SellingPlanFixedBillingPolicyInput),
            'recurring' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SellingPlanRecurringBillingPolicyInput),
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
