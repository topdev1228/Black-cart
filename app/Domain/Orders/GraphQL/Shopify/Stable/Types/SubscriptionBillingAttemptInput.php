<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $idempotencyKey
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionBillingCycleSelector|null $billingCycleSelector
 * @property mixed|null $originTime
 */
class SubscriptionBillingAttemptInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $idempotencyKey
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionBillingCycleSelector|null $billingCycleSelector
     * @param mixed|null $originTime
     */
    public static function make(
        $idempotencyKey,
        $billingCycleSelector = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $originTime = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($idempotencyKey !== self::UNDEFINED) {
            $instance->idempotencyKey = $idempotencyKey;
        }
        if ($billingCycleSelector !== self::UNDEFINED) {
            $instance->billingCycleSelector = $billingCycleSelector;
        }
        if ($originTime !== self::UNDEFINED) {
            $instance->originTime = $originTime;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'idempotencyKey' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'billingCycleSelector' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionBillingCycleSelector),
            'originTime' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
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
