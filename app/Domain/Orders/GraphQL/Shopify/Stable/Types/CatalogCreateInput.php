<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CatalogContextInput $context
 * @property string $status
 * @property string $title
 * @property int|string|null $priceListId
 * @property int|string|null $publicationId
 */
class CatalogCreateInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CatalogContextInput $context
     * @param string $status
     * @param string $title
     * @param int|string|null $priceListId
     * @param int|string|null $publicationId
     */
    public static function make(
        $context,
        $status,
        $title,
        $priceListId = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $publicationId = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($context !== self::UNDEFINED) {
            $instance->context = $context;
        }
        if ($status !== self::UNDEFINED) {
            $instance->status = $status;
        }
        if ($title !== self::UNDEFINED) {
            $instance->title = $title;
        }
        if ($priceListId !== self::UNDEFINED) {
            $instance->priceListId = $priceListId;
        }
        if ($publicationId !== self::UNDEFINED) {
            $instance->publicationId = $publicationId;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'context' => new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CatalogContextInput),
            'status' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'title' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'priceListId' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'publicationId' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
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
