<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string $inventoryItemId
 * @property mixed $ledgerDocumentUri
 * @property int|string $locationId
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\InventoryScheduledChangeInput> $scheduledChanges
 */
class InventoryScheduledChangeItemInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string $inventoryItemId
     * @param mixed $ledgerDocumentUri
     * @param int|string $locationId
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\InventoryScheduledChangeInput> $scheduledChanges
     */
    public static function make($inventoryItemId, $ledgerDocumentUri, $locationId, $scheduledChanges): self
    {
        $instance = new self;

        if ($inventoryItemId !== self::UNDEFINED) {
            $instance->inventoryItemId = $inventoryItemId;
        }
        if ($ledgerDocumentUri !== self::UNDEFINED) {
            $instance->ledgerDocumentUri = $ledgerDocumentUri;
        }
        if ($locationId !== self::UNDEFINED) {
            $instance->locationId = $locationId;
        }
        if ($scheduledChanges !== self::UNDEFINED) {
            $instance->scheduledChanges = $scheduledChanges;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'inventoryItemId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'ledgerDocumentUri' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'locationId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'scheduledChanges' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\InventoryScheduledChangeInput))),
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
