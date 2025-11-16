<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string $paymentTermsId
 */
class PaymentTermsDeleteInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string $paymentTermsId
     */
    public static function make($paymentTermsId): self
    {
        $instance = new self;

        if ($paymentTermsId !== self::UNDEFINED) {
            $instance->paymentTermsId = $paymentTermsId;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
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
