<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionDraftInput $contract
 * @property string $currencyCode
 * @property int|string $customerId
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionAtomicLineInput> $lines
 * @property mixed $nextBillingDate
 * @property array<string>|null $discountCodes
 */
class SubscriptionContractAtomicCreateInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionDraftInput $contract
     * @param string $currencyCode
     * @param int|string $customerId
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionAtomicLineInput> $lines
     * @param mixed $nextBillingDate
     * @param array<string>|null $discountCodes
     */
    public static function make(
        $contract,
        $currencyCode,
        $customerId,
        $lines,
        $nextBillingDate,
        $discountCodes = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($contract !== self::UNDEFINED) {
            $instance->contract = $contract;
        }
        if ($currencyCode !== self::UNDEFINED) {
            $instance->currencyCode = $currencyCode;
        }
        if ($customerId !== self::UNDEFINED) {
            $instance->customerId = $customerId;
        }
        if ($lines !== self::UNDEFINED) {
            $instance->lines = $lines;
        }
        if ($nextBillingDate !== self::UNDEFINED) {
            $instance->nextBillingDate = $nextBillingDate;
        }
        if ($discountCodes !== self::UNDEFINED) {
            $instance->discountCodes = $discountCodes;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'contract' => new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionDraftInput),
            'currencyCode' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'customerId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'lines' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionAtomicLineInput))),
            'nextBillingDate' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'discountCodes' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter))),
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
