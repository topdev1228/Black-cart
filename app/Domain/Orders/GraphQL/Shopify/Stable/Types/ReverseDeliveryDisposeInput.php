<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $dispositionType
 * @property int $quantity
 * @property int|string $reverseDeliveryLineItemId
 * @property int|string|null $locationId
 */
class ReverseDeliveryDisposeInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $dispositionType
     * @param int $quantity
     * @param int|string $reverseDeliveryLineItemId
     * @param int|string|null $locationId
     */
    public static function make(
        $dispositionType,
        $quantity,
        $reverseDeliveryLineItemId,
        $locationId = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($dispositionType !== self::UNDEFINED) {
            $instance->dispositionType = $dispositionType;
        }
        if ($quantity !== self::UNDEFINED) {
            $instance->quantity = $quantity;
        }
        if ($reverseDeliveryLineItemId !== self::UNDEFINED) {
            $instance->reverseDeliveryLineItemId = $reverseDeliveryLineItemId;
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
            'dispositionType' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'quantity' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'reverseDeliveryLineItemId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'locationId' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
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
