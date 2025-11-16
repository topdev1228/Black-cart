<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int $delta
 * @property int|string $inventoryItemId
 * @property int|string $locationId
 * @property string|null $ledgerDocumentUri
 */
class InventoryChangeInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int $delta
     * @param int|string $inventoryItemId
     * @param int|string $locationId
     * @param string|null $ledgerDocumentUri
     */
    public static function make(
        $delta,
        $inventoryItemId,
        $locationId,
        $ledgerDocumentUri = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($delta !== self::UNDEFINED) {
            $instance->delta = $delta;
        }
        if ($inventoryItemId !== self::UNDEFINED) {
            $instance->inventoryItemId = $inventoryItemId;
        }
        if ($locationId !== self::UNDEFINED) {
            $instance->locationId = $locationId;
        }
        if ($ledgerDocumentUri !== self::UNDEFINED) {
            $instance->ledgerDocumentUri = $ledgerDocumentUri;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'delta' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'inventoryItemId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'locationId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'ledgerDocumentUri' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
