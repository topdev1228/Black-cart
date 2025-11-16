<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $reason
 * @property mixed|null $billingDate
 * @property bool|null $skip
 */
class SubscriptionBillingCycleScheduleEditInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $reason
     * @param mixed|null $billingDate
     * @param bool|null $skip
     */
    public static function make(
        $reason,
        $billingDate = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $skip = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($reason !== self::UNDEFINED) {
            $instance->reason = $reason;
        }
        if ($billingDate !== self::UNDEFINED) {
            $instance->billingDate = $billingDate;
        }
        if ($skip !== self::UNDEFINED) {
            $instance->skip = $skip;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'reason' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'billingDate' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'skip' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
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
