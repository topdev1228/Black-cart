<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string $id
 * @property float|int|null $criteria
 * @property string|null $criteriaUnit
 * @property string|null $field
 * @property string|null $operator
 */
class DeliveryUpdateConditionInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string $id
     * @param float|int|null $criteria
     * @param string|null $criteriaUnit
     * @param string|null $field
     * @param string|null $operator
     */
    public static function make(
        $id,
        $criteria = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $criteriaUnit = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $field = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $operator = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($id !== self::UNDEFINED) {
            $instance->id = $id;
        }
        if ($criteria !== self::UNDEFINED) {
            $instance->criteria = $criteria;
        }
        if ($criteriaUnit !== self::UNDEFINED) {
            $instance->criteriaUnit = $criteriaUnit;
        }
        if ($field !== self::UNDEFINED) {
            $instance->field = $field;
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
            'id' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'criteria' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\FloatConverter),
            'criteriaUnit' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'field' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
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
