<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MailingAddressInput $billingAddress
 * @property string $billingAgreementId
 */
class RemotePaypalPaymentMethodInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MailingAddressInput $billingAddress
     * @param string $billingAgreementId
     */
    public static function make($billingAddress, $billingAgreementId): self
    {
        $instance = new self;

        if ($billingAddress !== self::UNDEFINED) {
            $instance->billingAddress = $billingAddress;
        }
        if ($billingAgreementId !== self::UNDEFINED) {
            $instance->billingAgreementId = $billingAgreementId;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'billingAddress' => new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MailingAddressInput),
            'billingAgreementId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
