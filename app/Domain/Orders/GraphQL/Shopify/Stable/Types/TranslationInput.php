<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $key
 * @property string $locale
 * @property string $translatableContentDigest
 * @property string $value
 * @property int|string|null $marketId
 */
class TranslationInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $key
     * @param string $locale
     * @param string $translatableContentDigest
     * @param string $value
     * @param int|string|null $marketId
     */
    public static function make(
        $key,
        $locale,
        $translatableContentDigest,
        $value,
        $marketId = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($key !== self::UNDEFINED) {
            $instance->key = $key;
        }
        if ($locale !== self::UNDEFINED) {
            $instance->locale = $locale;
        }
        if ($translatableContentDigest !== self::UNDEFINED) {
            $instance->translatableContentDigest = $translatableContentDigest;
        }
        if ($value !== self::UNDEFINED) {
            $instance->value = $value;
        }
        if ($marketId !== self::UNDEFINED) {
            $instance->marketId = $marketId;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'key' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'locale' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'translatableContentDigest' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'value' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'marketId' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
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
