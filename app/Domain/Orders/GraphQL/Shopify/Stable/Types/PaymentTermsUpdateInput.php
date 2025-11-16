<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PaymentTermsInput $paymentTermsAttributes
 * @property int|string $paymentTermsId
 */
class PaymentTermsUpdateInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PaymentTermsInput $paymentTermsAttributes
     * @param int|string $paymentTermsId
     */
    public static function make($paymentTermsAttributes, $paymentTermsId): self
    {
        $instance = new self;

        if ($paymentTermsAttributes !== self::UNDEFINED) {
            $instance->paymentTermsAttributes = $paymentTermsAttributes;
        }
        if ($paymentTermsId !== self::UNDEFINED) {
            $instance->paymentTermsId = $paymentTermsId;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'paymentTermsAttributes' => new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PaymentTermsInput),
            'paymentTermsId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
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
