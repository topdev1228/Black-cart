<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property mixed $start
 * @property mixed|null $end
 */
class PriceRuleValidityPeriodInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param mixed $start
     * @param mixed|null $end
     */
    public static function make(
        $start,
        $end = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($start !== self::UNDEFINED) {
            $instance->start = $start;
        }
        if ($end !== self::UNDEFINED) {
            $instance->end = $end;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'start' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'end' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
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
