<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $customerId
 * @property string|null $paymentMethodToken
 */
class RemoteBraintreePaymentMethodInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $customerId
     * @param string|null $paymentMethodToken
     */
    public static function make(
        $customerId,
        $paymentMethodToken = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($customerId !== self::UNDEFINED) {
            $instance->customerId = $customerId;
        }
        if ($paymentMethodToken !== self::UNDEFINED) {
            $instance->paymentMethodToken = $paymentMethodToken;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'customerId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'paymentMethodToken' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
