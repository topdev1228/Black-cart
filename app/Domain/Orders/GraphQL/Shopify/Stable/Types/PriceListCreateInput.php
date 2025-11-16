<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $currency
 * @property string $name
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceListParentCreateInput $parent
 * @property int|string|null $catalogId
 */
class PriceListCreateInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $currency
     * @param string $name
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceListParentCreateInput $parent
     * @param int|string|null $catalogId
     */
    public static function make(
        $currency,
        $name,
        $parent,
        $catalogId = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($currency !== self::UNDEFINED) {
            $instance->currency = $currency;
        }
        if ($name !== self::UNDEFINED) {
            $instance->name = $name;
        }
        if ($parent !== self::UNDEFINED) {
            $instance->parent = $parent;
        }
        if ($catalogId !== self::UNDEFINED) {
            $instance->catalogId = $catalogId;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'currency' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'name' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'parent' => new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceListParentCreateInput),
            'catalogId' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
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
