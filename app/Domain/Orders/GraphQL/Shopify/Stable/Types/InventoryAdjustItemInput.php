<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int $availableDelta
 * @property int|string $inventoryItemId
 */
class InventoryAdjustItemInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int $availableDelta
     * @param int|string $inventoryItemId
     */
    public static function make($availableDelta, $inventoryItemId): self
    {
        $instance = new self;

        if ($availableDelta !== self::UNDEFINED) {
            $instance->availableDelta = $availableDelta;
        }
        if ($inventoryItemId !== self::UNDEFINED) {
            $instance->inventoryItemId = $inventoryItemId;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'availableDelta' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'inventoryItemId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
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
