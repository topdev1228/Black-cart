<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MailingAddressInput|null $address
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionDeliveryMethodLocalDeliveryOptionInput|null $localDeliveryOption
 */
class SubscriptionDeliveryMethodLocalDeliveryInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MailingAddressInput|null $address
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionDeliveryMethodLocalDeliveryOptionInput|null $localDeliveryOption
     */
    public static function make(
        $address = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $localDeliveryOption = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($address !== self::UNDEFINED) {
            $instance->address = $address;
        }
        if ($localDeliveryOption !== self::UNDEFINED) {
            $instance->localDeliveryOption = $localDeliveryOption;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'address' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MailingAddressInput),
            'localDeliveryOption' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionDeliveryMethodLocalDeliveryOptionInput),
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
