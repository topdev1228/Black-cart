<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property mixed|null $fixedAmountValue
 * @property float|int|null $percentageValue
 */
class PriceRuleValueInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param mixed|null $fixedAmountValue
     * @param float|int|null $percentageValue
     */
    public static function make(
        $fixedAmountValue = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $percentageValue = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($fixedAmountValue !== self::UNDEFINED) {
            $instance->fixedAmountValue = $fixedAmountValue;
        }
        if ($percentageValue !== self::UNDEFINED) {
            $instance->percentageValue = $percentageValue;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'fixedAmountValue' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'percentageValue' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\FloatConverter),
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
