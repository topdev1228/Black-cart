<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\CollectionPublicationInput> $collectionPublications
 * @property int|string $id
 */
class CollectionUnpublishInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\CollectionPublicationInput> $collectionPublications
     * @param int|string $id
     */
    public static function make($collectionPublications, $id): self
    {
        $instance = new self;

        if ($collectionPublications !== self::UNDEFINED) {
            $instance->collectionPublications = $collectionPublications;
        }
        if ($id !== self::UNDEFINED) {
            $instance->id = $id;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'collectionPublications' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CollectionPublicationInput))),
            'id' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
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
