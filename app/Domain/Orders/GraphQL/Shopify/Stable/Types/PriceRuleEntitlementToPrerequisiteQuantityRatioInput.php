<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|null $entitlementQuantity
 * @property int|null $prerequisiteQuantity
 */
class PriceRuleEntitlementToPrerequisiteQuantityRatioInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|null $entitlementQuantity
     * @param int|null $prerequisiteQuantity
     */
    public static function make(
        $entitlementQuantity = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $prerequisiteQuantity = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($entitlementQuantity !== self::UNDEFINED) {
            $instance->entitlementQuantity = $entitlementQuantity;
        }
        if ($prerequisiteQuantity !== self::UNDEFINED) {
            $instance->prerequisiteQuantity = $prerequisiteQuantity;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'entitlementQuantity' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'prerequisiteQuantity' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
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
