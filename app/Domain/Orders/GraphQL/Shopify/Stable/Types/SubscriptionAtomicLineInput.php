<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionLineInput $line
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionAtomicManualDiscountInput>|null $discounts
 */
class SubscriptionAtomicLineInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionLineInput $line
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionAtomicManualDiscountInput>|null $discounts
     */
    public static function make(
        $line,
        $discounts = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($line !== self::UNDEFINED) {
            $instance->line = $line;
        }
        if ($discounts !== self::UNDEFINED) {
            $instance->discounts = $discounts;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'line' => new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionLineInput),
            'discounts' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionAtomicManualDiscountInput))),
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
