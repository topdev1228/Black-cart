<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property bool|null $all
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountCountriesInput|null $countries
 */
class DiscountShippingDestinationSelectionInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param bool|null $all
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountCountriesInput|null $countries
     */
    public static function make(
        $all = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $countries = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($all !== self::UNDEFINED) {
            $instance->all = $all;
        }
        if ($countries !== self::UNDEFINED) {
            $instance->countries = $countries;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'all' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'countries' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountCountriesInput),
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
