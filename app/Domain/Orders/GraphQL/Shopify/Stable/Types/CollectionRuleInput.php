<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $column
 * @property string $condition
 * @property string $relation
 * @property int|string|null $conditionObjectId
 */
class CollectionRuleInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $column
     * @param string $condition
     * @param string $relation
     * @param int|string|null $conditionObjectId
     */
    public static function make(
        $column,
        $condition,
        $relation,
        $conditionObjectId = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($column !== self::UNDEFINED) {
            $instance->column = $column;
        }
        if ($condition !== self::UNDEFINED) {
            $instance->condition = $condition;
        }
        if ($relation !== self::UNDEFINED) {
            $instance->relation = $relation;
        }
        if ($conditionObjectId !== self::UNDEFINED) {
            $instance->conditionObjectId = $conditionObjectId;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'column' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'condition' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'relation' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'conditionObjectId' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
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
