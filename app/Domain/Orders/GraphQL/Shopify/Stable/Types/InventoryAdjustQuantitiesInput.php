<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\InventoryChangeInput> $changes
 * @property string $name
 * @property string $reason
 * @property string|null $referenceDocumentUri
 */
class InventoryAdjustQuantitiesInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\InventoryChangeInput> $changes
     * @param string $name
     * @param string $reason
     * @param string|null $referenceDocumentUri
     */
    public static function make(
        $changes,
        $name,
        $reason,
        $referenceDocumentUri = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($changes !== self::UNDEFINED) {
            $instance->changes = $changes;
        }
        if ($name !== self::UNDEFINED) {
            $instance->name = $name;
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
            'changes' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\InventoryChangeInput))),
            'name' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'reason' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
