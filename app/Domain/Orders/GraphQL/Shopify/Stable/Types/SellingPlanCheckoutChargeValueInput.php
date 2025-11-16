<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property mixed|null $fixedValue
 * @property float|int|null $percentage
 */
class SellingPlanCheckoutChargeValueInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param mixed|null $fixedValue
     * @param float|int|null $percentage
     */
    public static function make(
        $fixedValue = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $percentage = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($fixedValue !== self::UNDEFINED) {
            $instance->fixedValue = $fixedValue;
        }
        if ($percentage !== self::UNDEFINED) {
            $instance->percentage = $percentage;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'fixedValue' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'percentage' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\FloatConverter),
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
