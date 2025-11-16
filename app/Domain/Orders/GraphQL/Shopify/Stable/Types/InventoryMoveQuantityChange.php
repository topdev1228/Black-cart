<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\InventoryMoveQuantityTerminalInput $from
 * @property int|string $inventoryItemId
 * @property int $quantity
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\InventoryMoveQuantityTerminalInput $to
 */
class InventoryMoveQuantityChange extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\InventoryMoveQuantityTerminalInput $from
     * @param int|string $inventoryItemId
     * @param int $quantity
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\InventoryMoveQuantityTerminalInput $to
     */
    public static function make($from, $inventoryItemId, $quantity, $to): self
    {
        $instance = new self;

        if ($from !== self::UNDEFINED) {
            $instance->from = $from;
        }
        if ($inventoryItemId !== self::UNDEFINED) {
            $instance->inventoryItemId = $inventoryItemId;
        }
        if ($quantity !== self::UNDEFINED) {
            $instance->quantity = $quantity;
        }
        if ($to !== self::UNDEFINED) {
            $instance->to = $to;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'from' => new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\InventoryMoveQuantityTerminalInput),
            'inventoryItemId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'quantity' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'to' => new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\InventoryMoveQuantityTerminalInput),
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
