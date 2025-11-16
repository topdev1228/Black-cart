<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string|null $code
 * @property bool|null $includeAllProvinces
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\DeliveryProvinceInput>|null $provinces
 * @property bool|null $restOfWorld
 */
class DeliveryCountryInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string|null $code
     * @param bool|null $includeAllProvinces
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\DeliveryProvinceInput>|null $provinces
     * @param bool|null $restOfWorld
     */
    public static function make(
        $code = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $includeAllProvinces = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $provinces = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $restOfWorld = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($code !== self::UNDEFINED) {
            $instance->code = $code;
        }
        if ($includeAllProvinces !== self::UNDEFINED) {
            $instance->includeAllProvinces = $includeAllProvinces;
        }
        if ($provinces !== self::UNDEFINED) {
            $instance->provinces = $provinces;
        }
        if ($restOfWorld !== self::UNDEFINED) {
            $instance->restOfWorld = $restOfWorld;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'code' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'includeAllProvinces' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'provinces' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DeliveryProvinceInput))),
            'restOfWorld' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
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
