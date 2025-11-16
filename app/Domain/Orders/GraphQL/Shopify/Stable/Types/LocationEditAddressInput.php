<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string|null $address1
 * @property string|null $address2
 * @property string|null $city
 * @property string|null $countryCode
 * @property string|null $phone
 * @property string|null $provinceCode
 * @property string|null $zip
 */
class LocationEditAddressInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string|null $address1
     * @param string|null $address2
     * @param string|null $city
     * @param string|null $countryCode
     * @param string|null $phone
     * @param string|null $provinceCode
     * @param string|null $zip
     */
    public static function make(
        $address1 = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $address2 = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $city = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $countryCode = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $phone = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $provinceCode = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $zip = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($address1 !== self::UNDEFINED) {
            $instance->address1 = $address1;
        }
        if ($address2 !== self::UNDEFINED) {
            $instance->address2 = $address2;
        }
        if ($city !== self::UNDEFINED) {
            $instance->city = $city;
        }
        if ($countryCode !== self::UNDEFINED) {
            $instance->countryCode = $countryCode;
        }
        if ($phone !== self::UNDEFINED) {
            $instance->phone = $phone;
        }
        if ($provinceCode !== self::UNDEFINED) {
            $instance->provinceCode = $provinceCode;
        }
        if ($zip !== self::UNDEFINED) {
            $instance->zip = $zip;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'address1' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'address2' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'city' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'countryCode' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'phone' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'provinceCode' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'zip' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
