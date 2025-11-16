<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $reason
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\InventorySetQuantityInput> $setQuantities
 * @property string|null $referenceDocumentUri
 */
class InventorySetOnHandQuantitiesInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $reason
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\InventorySetQuantityInput> $setQuantities
     * @param string|null $referenceDocumentUri
     */
    public static function make(
        $reason,
        $setQuantities,
        $referenceDocumentUri = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($reason !== self::UNDEFINED) {
            $instance->reason = $reason;
        }
        if ($setQuantities !== self::UNDEFINED) {
            $instance->setQuantities = $setQuantities;
        }
        if ($referenceDocumentUri !== self::UNDEFINED) {
            $instance->referenceDocumentUri = $referenceDocumentUri;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'reason' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'setQuantities' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\InventorySetQuantityInput))),
            'referenceDocumentUri' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
