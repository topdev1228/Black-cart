<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $defaultLocale
 * @property array<string>|null $alternateLocales
 * @property int|string|null $domainId
 * @property string|null $subfolderSuffix
 */
class MarketWebPresenceCreateInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $defaultLocale
     * @param array<string>|null $alternateLocales
     * @param int|string|null $domainId
     * @param string|null $subfolderSuffix
     */
    public static function make(
        $defaultLocale,
        $alternateLocales = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $domainId = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $subfolderSuffix = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($defaultLocale !== self::UNDEFINED) {
            $instance->defaultLocale = $defaultLocale;
        }
        if ($alternateLocales !== self::UNDEFINED) {
            $instance->alternateLocales = $alternateLocales;
        }
        if ($domainId !== self::UNDEFINED) {
            $instance->domainId = $domainId;
        }
        if ($subfolderSuffix !== self::UNDEFINED) {
            $instance->subfolderSuffix = $subfolderSuffix;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'defaultLocale' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'alternateLocales' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter))),
            'domainId' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'subfolderSuffix' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
