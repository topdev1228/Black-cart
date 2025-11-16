<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\WeightInput|null $criteria
 * @property string|null $operator
 */
class DeliveryWeightConditionInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\WeightInput|null $criteria
     * @param string|null $operator
     */
    public static function make(
        $criteria = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $operator = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($criteria !== self::UNDEFINED) {
            $instance->criteria = $criteria;
        }
        if ($operator !== self::UNDEFINED) {
            $instance->operator = $operator;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'criteria' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\WeightInput),
            'operator' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
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
