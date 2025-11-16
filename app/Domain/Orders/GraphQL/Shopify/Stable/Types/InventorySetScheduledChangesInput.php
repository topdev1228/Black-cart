<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\InventoryScheduledChangeItemInput> $items
 * @property string $reason
 * @property mixed $referenceDocumentUri
 */
class InventorySetScheduledChangesInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\InventoryScheduledChangeItemInput> $items
     * @param string $reason
     * @param mixed $referenceDocumentUri
     */
    public static function make($items, $reason, $referenceDocumentUri): self
    {
        $instance = new self;

        if ($items !== self::UNDEFINED) {
            $instance->items = $items;
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
            'items' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\InventoryScheduledChangeItemInput))),
            'reason' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'referenceDocumentUri' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
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
