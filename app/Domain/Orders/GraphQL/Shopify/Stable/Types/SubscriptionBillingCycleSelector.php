<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property mixed|null $date
 * @property int|null $index
 */
class SubscriptionBillingCycleSelector extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param mixed|null $date
     * @param int|null $index
     */
    public static function make(
        $date = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $index = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($date !== self::UNDEFINED) {
            $instance->date = $date;
        }
        if ($index !== self::UNDEFINED) {
            $instance->index = $index;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'date' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'index' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
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
