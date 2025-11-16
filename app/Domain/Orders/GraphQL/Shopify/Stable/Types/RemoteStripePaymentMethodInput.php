<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $customerId
 * @property string|null $paymentMethodId
 */
class RemoteStripePaymentMethodInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $customerId
     * @param string|null $paymentMethodId
     */
    public static function make(
        $customerId,
        $paymentMethodId = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($customerId !== self::UNDEFINED) {
            $instance->customerId = $customerId;
        }
        if ($paymentMethodId !== self::UNDEFINED) {
            $instance->paymentMethodId = $paymentMethodId;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'customerId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'paymentMethodId' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
