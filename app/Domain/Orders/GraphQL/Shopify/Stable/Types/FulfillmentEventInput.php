<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string $fulfillmentId
 * @property string $status
 * @property string|null $address1
 * @property string|null $city
 * @property string|null $country
 * @property mixed|null $estimatedDeliveryAt
 * @property mixed|null $happenedAt
 * @property float|int|null $latitude
 * @property float|int|null $longitude
 * @property string|null $message
 * @property string|null $province
 * @property string|null $zip
 */
class FulfillmentEventInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string $fulfillmentId
     * @param string $status
     * @param string|null $address1
     * @param string|null $city
     * @param string|null $country
     * @param mixed|null $estimatedDeliveryAt
     * @param mixed|null $happenedAt
     * @param float|int|null $latitude
     * @param float|int|null $longitude
     * @param string|null $message
     * @param string|null $province
     * @param string|null $zip
     */
    public static function make(
        $fulfillmentId,
        $status,
        $address1 = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $city = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $country = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $estimatedDeliveryAt = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $happenedAt = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $latitude = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $longitude = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $message = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $province = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $zip = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($fulfillmentId !== self::UNDEFINED) {
            $instance->fulfillmentId = $fulfillmentId;
        }
        if ($status !== self::UNDEFINED) {
            $instance->status = $status;
        }
        if ($address1 !== self::UNDEFINED) {
            $instance->address1 = $address1;
        }
        if ($city !== self::UNDEFINED) {
            $instance->city = $city;
        }
        if ($country !== self::UNDEFINED) {
            $instance->country = $country;
        }
        if ($estimatedDeliveryAt !== self::UNDEFINED) {
            $instance->estimatedDeliveryAt = $estimatedDeliveryAt;
        }
        if ($happenedAt !== self::UNDEFINED) {
            $instance->happenedAt = $happenedAt;
        }
        if ($latitude !== self::UNDEFINED) {
            $instance->latitude = $latitude;
        }
        if ($longitude !== self::UNDEFINED) {
            $instance->longitude = $longitude;
        }
        if ($message !== self::UNDEFINED) {
            $instance->message = $message;
        }
        if ($province !== self::UNDEFINED) {
            $instance->province = $province;
        }
        if ($zip !== self::UNDEFINED) {
            $instance->zip = $zip;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'fulfillmentId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'status' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'address1' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'city' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'country' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'estimatedDeliveryAt' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'happenedAt' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'latitude' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\FloatConverter),
            'longitude' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\FloatConverter),
            'message' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'province' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'zip' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
