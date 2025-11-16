<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $name
 * @property string $query
 * @property string $resourceType
 */
class SavedSearchCreateInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $name
     * @param string $query
     * @param string $resourceType
     */
    public static function make($name, $query, $resourceType): self
    {
        $instance = new self;

        if ($name !== self::UNDEFINED) {
            $instance->name = $name;
        }
        if ($query !== self::UNDEFINED) {
            $instance->query = $query;
        }
        if ($resourceType !== self::UNDEFINED) {
            $instance->resourceType = $resourceType;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'name' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'query' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'resourceType' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
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
