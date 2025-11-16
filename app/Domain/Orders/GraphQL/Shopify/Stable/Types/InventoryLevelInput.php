<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int $availableQuantity
 * @property int|string $locationId
 */
class InventoryLevelInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int $availableQuantity
     * @param int|string $locationId
     */
    public static function make($availableQuantity, $locationId): self
    {
        $instance = new self;

        if ($availableQuantity !== self::UNDEFINED) {
            $instance->availableQuantity = $availableQuantity;
        }
        if ($locationId !== self::UNDEFINED) {
            $instance->locationId = $locationId;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'availableQuantity' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'locationId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
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
