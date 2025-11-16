<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string $paymentTermsTemplateId
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\PaymentScheduleInput>|null $paymentSchedules
 */
class PaymentTermsCreateInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string $paymentTermsTemplateId
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\PaymentScheduleInput>|null $paymentSchedules
     */
    public static function make(
        $paymentTermsTemplateId,
        $paymentSchedules = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($paymentTermsTemplateId !== self::UNDEFINED) {
            $instance->paymentTermsTemplateId = $paymentTermsTemplateId;
        }
        if ($paymentSchedules !== self::UNDEFINED) {
            $instance->paymentSchedules = $paymentSchedules;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'paymentTermsTemplateId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'paymentSchedules' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PaymentScheduleInput))),
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
