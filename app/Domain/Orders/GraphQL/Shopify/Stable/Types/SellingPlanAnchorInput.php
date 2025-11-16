<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|null $cutoffDay
 * @property int|null $day
 * @property int|null $month
 * @property string|null $type
 */
class SellingPlanAnchorInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|null $cutoffDay
     * @param int|null $day
     * @param int|null $month
     * @param string|null $type
     */
    public static function make(
        $cutoffDay = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $day = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $month = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $type = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($cutoffDay !== self::UNDEFINED) {
            $instance->cutoffDay = $cutoffDay;
        }
        if ($day !== self::UNDEFINED) {
            $instance->day = $day;
        }
        if ($month !== self::UNDEFINED) {
            $instance->month = $month;
        }
        if ($type !== self::UNDEFINED) {
            $instance->type = $type;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'cutoffDay' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'day' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'month' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'type' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
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
