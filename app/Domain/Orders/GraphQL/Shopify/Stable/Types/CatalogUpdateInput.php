<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CatalogContextInput|null $context
 * @property int|string|null $priceListId
 * @property int|string|null $publicationId
 * @property string|null $status
 * @property string|null $title
 */
class CatalogUpdateInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CatalogContextInput|null $context
     * @param int|string|null $priceListId
     * @param int|string|null $publicationId
     * @param string|null $status
     * @param string|null $title
     */
    public static function make(
        $context = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $priceListId = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $publicationId = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $status = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $title = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($context !== self::UNDEFINED) {
            $instance->context = $context;
        }
        if ($priceListId !== self::UNDEFINED) {
            $instance->priceListId = $priceListId;
        }
        if ($publicationId !== self::UNDEFINED) {
            $instance->publicationId = $publicationId;
        }
        if ($status !== self::UNDEFINED) {
            $instance->status = $status;
        }
        if ($title !== self::UNDEFINED) {
            $instance->title = $title;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'context' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CatalogContextInput),
            'priceListId' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'publicationId' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'status' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'title' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
