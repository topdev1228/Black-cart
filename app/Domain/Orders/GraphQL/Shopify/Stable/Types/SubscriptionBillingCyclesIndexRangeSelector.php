<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int $endIndex
 * @property int $startIndex
 */
class SubscriptionBillingCyclesIndexRangeSelector extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int $endIndex
     * @param int $startIndex
     */
    public static function make($endIndex, $startIndex): self
    {
        $instance = new self;

        if ($endIndex !== self::UNDEFINED) {
            $instance->endIndex = $endIndex;
        }
        if ($startIndex !== self::UNDEFINED) {
            $instance->startIndex = $startIndex;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'endIndex' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'startIndex' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IntConverter),
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
