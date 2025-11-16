<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string $lineItemId
 * @property int $quantity
 * @property int|string|null $locationId
 * @property string|null $restockType
 */
class RefundLineItemInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string $lineItemId
     * @param int $quantity
     * @param int|string|null $locationId
     * @param string|null $restockType
     */
    public static function make(
        $lineItemId,
        $quantity,
        $locationId = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $restockType = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($lineItemId !== self::UNDEFINED) {
            $instance->lineItemId = $lineItemId;
        }
        if ($quantity !== self::UNDEFINED) {
            $instance->quantity = $quantity;
        }
        if ($locationId !== self::UNDEFINED) {
            $instance->locationId = $locationId;
        }
        if ($restockType !== self::UNDEFINED) {
            $instance->restockType = $restockType;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'lineItemId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'quantity' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'locationId' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'restockType' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
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
