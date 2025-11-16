<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int $availableDelta
 * @property int|string $inventoryLevelId
 */
class InventoryAdjustQuantityInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int $availableDelta
     * @param int|string $inventoryLevelId
     */
    public static function make($availableDelta, $inventoryLevelId): self
    {
        $instance = new self;

        if ($availableDelta !== self::UNDEFINED) {
            $instance->availableDelta = $availableDelta;
        }
        if ($inventoryLevelId !== self::UNDEFINED) {
            $instance->inventoryLevelId = $inventoryLevelId;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'availableDelta' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'inventoryLevelId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
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
