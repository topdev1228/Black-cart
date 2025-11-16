<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property mixed|null $greaterThan
 * @property mixed|null $greaterThanOrEqualTo
 * @property mixed|null $lessThan
 * @property mixed|null $lessThanOrEqualTo
 */
class PriceRuleMoneyRangeInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param mixed|null $greaterThan
     * @param mixed|null $greaterThanOrEqualTo
     * @param mixed|null $lessThan
     * @param mixed|null $lessThanOrEqualTo
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
            'greaterThan' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'greaterThanOrEqualTo' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'lessThan' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'lessThanOrEqualTo' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
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
