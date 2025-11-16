<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property float|int $value
 * @property string $valueType
 * @property mixed|null $amount
 * @property string|null $description
 * @property string|null $title
 */
class DraftOrderAppliedDiscountInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param float|int $value
     * @param string $valueType
     * @param mixed|null $amount
     * @param string|null $description
     * @param string|null $title
     */
    public static function make(
        $value,
        $valueType,
        $amount = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $description = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $title = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($value !== self::UNDEFINED) {
            $instance->value = $value;
        }
        if ($valueType !== self::UNDEFINED) {
            $instance->valueType = $valueType;
        }
        if ($amount !== self::UNDEFINED) {
            $instance->amount = $amount;
        }
        if ($description !== self::UNDEFINED) {
            $instance->description = $description;
        }
        if ($title !== self::UNDEFINED) {
            $instance->title = $title;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'value' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\FloatConverter),
            'valueType' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'amount' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'description' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'title' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
