<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property mixed|null $cost
 * @property string|null $countryCodeOfOrigin
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\CountryHarmonizedSystemCodeInput>|null $countryHarmonizedSystemCodes
 * @property string|null $harmonizedSystemCode
 * @property string|null $provinceCodeOfOrigin
 * @property bool|null $requiresShipping
 * @property bool|null $tracked
 */
class InventoryItemUpdateInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param mixed|null $cost
     * @param string|null $countryCodeOfOrigin
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\CountryHarmonizedSystemCodeInput>|null $countryHarmonizedSystemCodes
     * @param string|null $harmonizedSystemCode
     * @param string|null $provinceCodeOfOrigin
     * @param bool|null $requiresShipping
     * @param bool|null $tracked
     */
    public static function make(
        $cost = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $countryCodeOfOrigin = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $countryHarmonizedSystemCodes = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $harmonizedSystemCode = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $provinceCodeOfOrigin = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $requiresShipping = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $tracked = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($cost !== self::UNDEFINED) {
            $instance->cost = $cost;
        }
        if ($countryCodeOfOrigin !== self::UNDEFINED) {
            $instance->countryCodeOfOrigin = $countryCodeOfOrigin;
        }
        if ($countryHarmonizedSystemCodes !== self::UNDEFINED) {
            $instance->countryHarmonizedSystemCodes = $countryHarmonizedSystemCodes;
        }
        if ($harmonizedSystemCode !== self::UNDEFINED) {
            $instance->harmonizedSystemCode = $harmonizedSystemCode;
        }
        if ($provinceCodeOfOrigin !== self::UNDEFINED) {
            $instance->provinceCodeOfOrigin = $provinceCodeOfOrigin;
        }
        if ($requiresShipping !== self::UNDEFINED) {
            $instance->requiresShipping = $requiresShipping;
        }
        if ($tracked !== self::UNDEFINED) {
            $instance->tracked = $tracked;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'cost' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'countryCodeOfOrigin' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'countryHarmonizedSystemCodes' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CountryHarmonizedSystemCodeInput))),
            'harmonizedSystemCode' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'provinceCodeOfOrigin' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'requiresShipping' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'tracked' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
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
