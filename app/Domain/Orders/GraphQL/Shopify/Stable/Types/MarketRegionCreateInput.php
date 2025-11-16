<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $countryCode
 */
class MarketRegionCreateInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $countryCode
     */
    public static function make($countryCode): self
    {
        $instance = new self;

        if ($countryCode !== self::UNDEFINED) {
            $instance->countryCode = $countryCode;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'countryCode' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
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
