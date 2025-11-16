<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MoneyInput $cappedAmount
 * @property string $terms
 */
class AppUsagePricingInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MoneyInput $cappedAmount
     * @param string $terms
     */
    public static function make($cappedAmount, $terms): self
    {
        $instance = new self;

        if ($cappedAmount !== self::UNDEFINED) {
            $instance->cappedAmount = $cappedAmount;
        }
        if ($terms !== self::UNDEFINED) {
            $instance->terms = $terms;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'cappedAmount' => new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MoneyInput),
            'terms' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
