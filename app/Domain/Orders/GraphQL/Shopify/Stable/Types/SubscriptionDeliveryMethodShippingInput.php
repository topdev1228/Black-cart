<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MailingAddressInput|null $address
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionDeliveryMethodShippingOptionInput|null $shippingOption
 */
class SubscriptionDeliveryMethodShippingInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MailingAddressInput|null $address
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionDeliveryMethodShippingOptionInput|null $shippingOption
     */
    public static function make(
        $address = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $shippingOption = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($address !== self::UNDEFINED) {
            $instance->address = $address;
        }
        if ($shippingOption !== self::UNDEFINED) {
            $instance->shippingOption = $shippingOption;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'address' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MailingAddressInput),
            'shippingOption' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionDeliveryMethodShippingOptionInput),
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
