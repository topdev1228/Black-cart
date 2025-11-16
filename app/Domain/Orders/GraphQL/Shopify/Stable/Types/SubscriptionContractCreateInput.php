<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionDraftInput $contract
 * @property string $currencyCode
 * @property int|string $customerId
 * @property mixed $nextBillingDate
 */
class SubscriptionContractCreateInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionDraftInput $contract
     * @param string $currencyCode
     * @param int|string $customerId
     * @param mixed $nextBillingDate
     */
    public static function make($contract, $currencyCode, $customerId, $nextBillingDate): self
    {
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
        if ($nextBillingDate !== self::UNDEFINED) {
            $instance->nextBillingDate = $nextBillingDate;
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
            'nextBillingDate' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
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
