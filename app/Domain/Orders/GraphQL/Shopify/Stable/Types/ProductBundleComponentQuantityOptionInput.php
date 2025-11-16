<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $name
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\ProductBundleComponentQuantityOptionValueInput> $values
 */
class ProductBundleComponentQuantityOptionInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $name
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\ProductBundleComponentQuantityOptionValueInput> $values
     */
    public static function make($name, $values): self
    {
        $instance = new self;

        if ($name !== self::UNDEFINED) {
            $instance->name = $name;
        }
        if ($values !== self::UNDEFINED) {
            $instance->values = $values;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'name' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'values' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ProductBundleComponentQuantityOptionValueInput))),
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
