<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property bool|null $all
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionManualDiscountLinesInput|null $lines
 */
class SubscriptionManualDiscountEntitledLinesInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param bool|null $all
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionManualDiscountLinesInput|null $lines
     */
    public static function make(
        $all = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $lines = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($all !== self::UNDEFINED) {
            $instance->all = $all;
        }
        if ($lines !== self::UNDEFINED) {
            $instance->lines = $lines;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'all' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'lines' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionManualDiscountLinesInput),
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
