<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string|null $baseCurrency
 * @property bool|null $localCurrencies
 */
class MarketCurrencySettingsUpdateInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string|null $baseCurrency
     * @param bool|null $localCurrencies
     */
    public static function make(
        $baseCurrency = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $localCurrencies = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($baseCurrency !== self::UNDEFINED) {
            $instance->baseCurrency = $baseCurrency;
        }
        if ($localCurrencies !== self::UNDEFINED) {
            $instance->localCurrencies = $localCurrencies;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'baseCurrency' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'localCurrencies' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
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
