<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string|null $description
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MoneyInput|null $fixedValue
 * @property float|int|null $percentValue
 */
class OrderEditAppliedDiscountInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string|null $description
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MoneyInput|null $fixedValue
     * @param float|int|null $percentValue
     */
    public static function make(
        $description = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $fixedValue = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $percentValue = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($description !== self::UNDEFINED) {
            $instance->description = $description;
        }
        if ($fixedValue !== self::UNDEFINED) {
            $instance->fixedValue = $fixedValue;
        }
        if ($percentValue !== self::UNDEFINED) {
            $instance->percentValue = $percentValue;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'description' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'fixedValue' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MoneyInput),
            'percentValue' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\FloatConverter),
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
