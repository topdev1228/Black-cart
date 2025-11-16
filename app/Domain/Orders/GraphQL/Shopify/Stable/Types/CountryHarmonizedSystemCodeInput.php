<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $countryCode
 * @property string $harmonizedSystemCode
 */
class CountryHarmonizedSystemCodeInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $countryCode
     * @param string $harmonizedSystemCode
     */
    public static function make($countryCode, $harmonizedSystemCode): self
    {
        $instance = new self;

        if ($countryCode !== self::UNDEFINED) {
            $instance->countryCode = $countryCode;
        }
        if ($harmonizedSystemCode !== self::UNDEFINED) {
            $instance->harmonizedSystemCode = $harmonizedSystemCode;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'countryCode' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'harmonizedSystemCode' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
