<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property mixed|null $price
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MoneyInput|null $priceWithCurrency
 * @property string|null $shippingRateHandle
 * @property string|null $title
 */
class ShippingLineInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param mixed|null $price
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MoneyInput|null $priceWithCurrency
     * @param string|null $shippingRateHandle
     * @param string|null $title
     */
    public static function make(
        $price = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $priceWithCurrency = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $shippingRateHandle = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $title = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($price !== self::UNDEFINED) {
            $instance->price = $price;
        }
        if ($priceWithCurrency !== self::UNDEFINED) {
            $instance->priceWithCurrency = $priceWithCurrency;
        }
        if ($shippingRateHandle !== self::UNDEFINED) {
            $instance->shippingRateHandle = $shippingRateHandle;
        }
        if ($title !== self::UNDEFINED) {
            $instance->title = $title;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'price' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'priceWithCurrency' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MoneyInput),
            'shippingRateHandle' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'title' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
