<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\InventoryMoveQuantityChange> $changes
 * @property string $reason
 * @property string $referenceDocumentUri
 */
class InventoryMoveQuantitiesInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\InventoryMoveQuantityChange> $changes
     * @param string $reason
     * @param string $referenceDocumentUri
     */
    public static function make($changes, $reason, $referenceDocumentUri): self
    {
        $instance = new self;

        if ($changes !== self::UNDEFINED) {
            $instance->changes = $changes;
        }
        if ($reason !== self::UNDEFINED) {
            $instance->reason = $reason;
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
            'changes' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\InventoryMoveQuantityChange))),
            'reason' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'referenceDocumentUri' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
