<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property mixed $basePrice
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionPricingPolicyCycleDiscountsInput> $cycleDiscounts
 */
class SubscriptionPricingPolicyInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param mixed $basePrice
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionPricingPolicyCycleDiscountsInput> $cycleDiscounts
     */
    public static function make($basePrice, $cycleDiscounts): self
    {
        $instance = new self;

        if ($basePrice !== self::UNDEFINED) {
            $instance->basePrice = $basePrice;
        }
        if ($cycleDiscounts !== self::UNDEFINED) {
            $instance->cycleDiscounts = $cycleDiscounts;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'basePrice' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'cycleDiscounts' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionPricingPolicyCycleDiscountsInput))),
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
