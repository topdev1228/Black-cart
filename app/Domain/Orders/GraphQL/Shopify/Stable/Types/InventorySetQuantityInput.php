<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string $inventoryItemId
 * @property int|string $locationId
 * @property int $quantity
 */
class InventorySetQuantityInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string $inventoryItemId
     * @param int|string $locationId
     * @param int $quantity
     */
    public static function make($inventoryItemId, $locationId, $quantity): self
    {
        $instance = new self;

        if ($inventoryItemId !== self::UNDEFINED) {
            $instance->inventoryItemId = $inventoryItemId;
        }
        if ($locationId !== self::UNDEFINED) {
            $instance->locationId = $locationId;
        }
        if ($quantity !== self::UNDEFINED) {
            $instance->quantity = $quantity;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'inventoryItemId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'locationId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'quantity' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IntConverter),
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
