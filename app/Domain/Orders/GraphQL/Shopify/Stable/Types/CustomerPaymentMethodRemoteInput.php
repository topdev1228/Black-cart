<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\RemoteAuthorizeNetCustomerPaymentProfileInput|null $authorizeNetCustomerPaymentProfile
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\RemoteBraintreePaymentMethodInput|null $braintreePaymentMethod
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\RemotePaypalPaymentMethodInput|null $paypalPaymentMethod
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\RemoteStripePaymentMethodInput|null $stripePaymentMethod
 */
class CustomerPaymentMethodRemoteInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\RemoteAuthorizeNetCustomerPaymentProfileInput|null $authorizeNetCustomerPaymentProfile
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\RemoteBraintreePaymentMethodInput|null $braintreePaymentMethod
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\RemotePaypalPaymentMethodInput|null $paypalPaymentMethod
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\RemoteStripePaymentMethodInput|null $stripePaymentMethod
     */
    public static function make(
        $authorizeNetCustomerPaymentProfile = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $braintreePaymentMethod = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $paypalPaymentMethod = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $stripePaymentMethod = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($authorizeNetCustomerPaymentProfile !== self::UNDEFINED) {
            $instance->authorizeNetCustomerPaymentProfile = $authorizeNetCustomerPaymentProfile;
        }
        if ($braintreePaymentMethod !== self::UNDEFINED) {
            $instance->braintreePaymentMethod = $braintreePaymentMethod;
        }
        if ($paypalPaymentMethod !== self::UNDEFINED) {
            $instance->paypalPaymentMethod = $paypalPaymentMethod;
        }
        if ($stripePaymentMethod !== self::UNDEFINED) {
            $instance->stripePaymentMethod = $stripePaymentMethod;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'authorizeNetCustomerPaymentProfile' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\RemoteAuthorizeNetCustomerPaymentProfileInput),
            'braintreePaymentMethod' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\RemoteBraintreePaymentMethodInput),
            'paypalPaymentMethod' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\RemotePaypalPaymentMethodInput),
            'stripePaymentMethod' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\RemoteStripePaymentMethodInput),
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
