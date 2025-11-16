<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property mixed $endDate
 * @property mixed $startDate
 */
class SubscriptionBillingCyclesDateRangeSelector extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param mixed $endDate
     * @param mixed $startDate
     */
    public static function make($endDate, $startDate): self
    {
        $instance = new self;

        if ($endDate !== self::UNDEFINED) {
            $instance->endDate = $endDate;
        }
        if ($startDate !== self::UNDEFINED) {
            $instance->startDate = $startDate;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'endDate' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'startDate' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
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
