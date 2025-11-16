<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property array<int|string>|null $marketWebPresenceIds
 * @property bool|null $published
 */
class ShopLocaleInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param array<int|string>|null $marketWebPresenceIds
     * @param bool|null $published
     */
    public static function make(
        $marketWebPresenceIds = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $published = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($marketWebPresenceIds !== self::UNDEFINED) {
            $instance->marketWebPresenceIds = $marketWebPresenceIds;
        }
        if ($published !== self::UNDEFINED) {
            $instance->published = $published;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'marketWebPresenceIds' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter))),
            'published' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
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
