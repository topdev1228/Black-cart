<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property bool|null $adaptToNewServices
 * @property int|string|null $carrierServiceId
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MoneyInput|null $fixedFee
 * @property int|string|null $id
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\DeliveryParticipantServiceInput>|null $participantServices
 * @property float|int|null $percentageOfRateFee
 */
class DeliveryParticipantInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param bool|null $adaptToNewServices
     * @param int|string|null $carrierServiceId
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MoneyInput|null $fixedFee
     * @param int|string|null $id
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\DeliveryParticipantServiceInput>|null $participantServices
     * @param float|int|null $percentageOfRateFee
     */
    public static function make(
        $adaptToNewServices = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $carrierServiceId = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $fixedFee = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $id = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $participantServices = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $percentageOfRateFee = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($adaptToNewServices !== self::UNDEFINED) {
            $instance->adaptToNewServices = $adaptToNewServices;
        }
        if ($carrierServiceId !== self::UNDEFINED) {
            $instance->carrierServiceId = $carrierServiceId;
        }
        if ($fixedFee !== self::UNDEFINED) {
            $instance->fixedFee = $fixedFee;
        }
        if ($id !== self::UNDEFINED) {
            $instance->id = $id;
        }
        if ($participantServices !== self::UNDEFINED) {
            $instance->participantServices = $participantServices;
        }
        if ($percentageOfRateFee !== self::UNDEFINED) {
            $instance->percentageOfRateFee = $percentageOfRateFee;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'adaptToNewServices' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'carrierServiceId' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'fixedFee' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MoneyInput),
            'id' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'participantServices' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DeliveryParticipantServiceInput))),
            'percentageOfRateFee' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\FloatConverter),
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
