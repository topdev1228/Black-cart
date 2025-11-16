<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string $childProductId
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\SelectedVariantOptionInput> $selectedParentOptionValues
 */
class ChildProductRelationInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string $childProductId
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\SelectedVariantOptionInput> $selectedParentOptionValues
     */
    public static function make($childProductId, $selectedParentOptionValues): self
    {
        $instance = new self;

        if ($childProductId !== self::UNDEFINED) {
            $instance->childProductId = $childProductId;
        }
        if ($selectedParentOptionValues !== self::UNDEFINED) {
            $instance->selectedParentOptionValues = $selectedParentOptionValues;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'childProductId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'selectedParentOptionValues' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SelectedVariantOptionInput))),
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
