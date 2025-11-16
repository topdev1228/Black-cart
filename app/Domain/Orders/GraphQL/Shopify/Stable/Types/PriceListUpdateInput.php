<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string|null $catalogId
 * @property string|null $currency
 * @property string|null $name
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceListParentUpdateInput|null $parent
 */
class PriceListUpdateInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string|null $catalogId
     * @param string|null $currency
     * @param string|null $name
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceListParentUpdateInput|null $parent
     */
    public static function make(
        $catalogId = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $currency = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $name = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $parent = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($catalogId !== self::UNDEFINED) {
            $instance->catalogId = $catalogId;
        }
        if ($currency !== self::UNDEFINED) {
            $instance->currency = $currency;
        }
        if ($name !== self::UNDEFINED) {
            $instance->name = $name;
        }
        if ($parent !== self::UNDEFINED) {
            $instance->parent = $parent;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'catalogId' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'currency' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'name' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'parent' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PriceListParentUpdateInput),
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
