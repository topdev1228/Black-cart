<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property bool|null $orderDiscounts
 * @property bool|null $productDiscounts
 * @property bool|null $shippingDiscounts
 */
class DiscountCombinesWithInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param bool|null $orderDiscounts
     * @param bool|null $productDiscounts
     * @param bool|null $shippingDiscounts
     */
    public static function make(
        $orderDiscounts = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $productDiscounts = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $shippingDiscounts = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($orderDiscounts !== self::UNDEFINED) {
            $instance->orderDiscounts = $orderDiscounts;
        }
        if ($productDiscounts !== self::UNDEFINED) {
            $instance->productDiscounts = $productDiscounts;
        }
        if ($shippingDiscounts !== self::UNDEFINED) {
            $instance->shippingDiscounts = $shippingDiscounts;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'orderDiscounts' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'productDiscounts' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'shippingDiscounts' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
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
