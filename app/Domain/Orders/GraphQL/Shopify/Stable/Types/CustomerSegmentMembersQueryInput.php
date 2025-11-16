<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string|null $query
 * @property bool|null $reverse
 * @property int|string|null $segmentId
 * @property string|null $sortKey
 */
class CustomerSegmentMembersQueryInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string|null $query
     * @param bool|null $reverse
     * @param int|string|null $segmentId
     * @param string|null $sortKey
     */
    public static function make(
        $query = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $reverse = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $segmentId = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $sortKey = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($query !== self::UNDEFINED) {
            $instance->query = $query;
        }
        if ($reverse !== self::UNDEFINED) {
            $instance->reverse = $reverse;
        }
        if ($segmentId !== self::UNDEFINED) {
            $instance->segmentId = $segmentId;
        }
        if ($sortKey !== self::UNDEFINED) {
            $instance->sortKey = $sortKey;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'query' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'reverse' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'segmentId' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'sortKey' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
