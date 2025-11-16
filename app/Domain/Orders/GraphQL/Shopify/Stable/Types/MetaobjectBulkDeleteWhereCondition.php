<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property array<int|string>|null $ids
 * @property string|null $type
 */
class MetaobjectBulkDeleteWhereCondition extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param array<int|string>|null $ids
     * @param string|null $type
     */
    public static function make(
        $ids = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $type = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($ids !== self::UNDEFINED) {
            $instance->ids = $ids;
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
            'ids' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter))),
            'type' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
