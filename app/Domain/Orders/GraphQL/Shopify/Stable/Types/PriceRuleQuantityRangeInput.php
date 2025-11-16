<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|null $greaterThan
 * @property int|null $greaterThanOrEqualTo
 * @property int|null $lessThan
 * @property int|null $lessThanOrEqualTo
 */
class PriceRuleQuantityRangeInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|null $greaterThan
     * @param int|null $greaterThanOrEqualTo
     * @param int|null $lessThan
     * @param int|null $lessThanOrEqualTo
     */
    public static function make(
        $greaterThan = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $greaterThanOrEqualTo = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $lessThan = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $lessThanOrEqualTo = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($greaterThan !== self::UNDEFINED) {
            $instance->greaterThan = $greaterThan;
        }
        if ($greaterThanOrEqualTo !== self::UNDEFINED) {
            $instance->greaterThanOrEqualTo = $greaterThanOrEqualTo;
        }
        if ($lessThan !== self::UNDEFINED) {
            $instance->lessThan = $lessThan;
        }
        if ($lessThanOrEqualTo !== self::UNDEFINED) {
            $instance->lessThanOrEqualTo = $lessThanOrEqualTo;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'greaterThan' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'greaterThanOrEqualTo' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'lessThan' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'lessThanOrEqualTo' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
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
