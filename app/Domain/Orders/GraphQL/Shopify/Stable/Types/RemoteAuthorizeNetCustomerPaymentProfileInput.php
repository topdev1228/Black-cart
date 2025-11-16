<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $customerProfileId
 * @property string|null $customerPaymentProfileId
 */
class RemoteAuthorizeNetCustomerPaymentProfileInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $customerProfileId
     * @param string|null $customerPaymentProfileId
     */
    public static function make(
        $customerProfileId,
        $customerPaymentProfileId = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($customerProfileId !== self::UNDEFINED) {
            $instance->customerProfileId = $customerProfileId;
        }
        if ($customerPaymentProfileId !== self::UNDEFINED) {
            $instance->customerPaymentProfileId = $customerPaymentProfileId;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'customerProfileId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'customerPaymentProfileId' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
