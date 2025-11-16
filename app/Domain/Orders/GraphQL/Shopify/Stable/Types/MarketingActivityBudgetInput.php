<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string|null $budgetType
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MoneyInput|null $total
 */
class MarketingActivityBudgetInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string|null $budgetType
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MoneyInput|null $total
     */
    public static function make(
        $budgetType = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $total = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($budgetType !== self::UNDEFINED) {
            $instance->budgetType = $budgetType;
        }
        if ($total !== self::UNDEFINED) {
            $instance->total = $total;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'budgetType' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'total' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MoneyInput),
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
