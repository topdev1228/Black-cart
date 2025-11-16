<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $key
 * @property int|string $marketId
 * @property string $marketLocalizableContentDigest
 * @property string $value
 */
class MarketLocalizationRegisterInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $key
     * @param int|string $marketId
     * @param string $marketLocalizableContentDigest
     * @param string $value
     */
    public static function make($key, $marketId, $marketLocalizableContentDigest, $value): self
    {
        $instance = new self;

        if ($key !== self::UNDEFINED) {
            $instance->key = $key;
        }
        if ($marketId !== self::UNDEFINED) {
            $instance->marketId = $marketId;
        }
        if ($marketLocalizableContentDigest !== self::UNDEFINED) {
            $instance->marketLocalizableContentDigest = $marketLocalizableContentDigest;
        }
        if ($value !== self::UNDEFINED) {
            $instance->value = $value;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'key' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'marketId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'marketLocalizableContentDigest' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'value' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
