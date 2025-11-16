<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string $locationId
 * @property string $pickupTime
 * @property string|null $instructions
 */
class DeliveryLocationLocalPickupEnableInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string $locationId
     * @param string $pickupTime
     * @param string|null $instructions
     */
    public static function make(
        $locationId,
        $pickupTime,
        $instructions = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($locationId !== self::UNDEFINED) {
            $instance->locationId = $locationId;
        }
        if ($pickupTime !== self::UNDEFINED) {
            $instance->pickupTime = $pickupTime;
        }
        if ($instructions !== self::UNDEFINED) {
            $instance->instructions = $instructions;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'locationId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'pickupTime' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'instructions' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
