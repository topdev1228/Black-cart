<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\PaymentScheduleInput>|null $paymentSchedules
 * @property int|string|null $paymentTermsTemplateId
 */
class PaymentTermsInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\PaymentScheduleInput>|null $paymentSchedules
     * @param int|string|null $paymentTermsTemplateId
     */
    public static function make(
        $paymentSchedules = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $paymentTermsTemplateId = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($paymentSchedules !== self::UNDEFINED) {
            $instance->paymentSchedules = $paymentSchedules;
        }
        if ($paymentTermsTemplateId !== self::UNDEFINED) {
            $instance->paymentTermsTemplateId = $paymentTermsTemplateId;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'paymentSchedules' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PaymentScheduleInput))),
            'paymentTermsTemplateId' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
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
