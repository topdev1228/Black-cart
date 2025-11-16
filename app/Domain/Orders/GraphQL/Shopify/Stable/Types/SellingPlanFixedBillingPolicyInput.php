<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SellingPlanCheckoutChargeInput|null $checkoutCharge
 * @property mixed|null $remainingBalanceChargeExactTime
 * @property string|null $remainingBalanceChargeTimeAfterCheckout
 * @property string|null $remainingBalanceChargeTrigger
 */
class SellingPlanFixedBillingPolicyInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SellingPlanCheckoutChargeInput|null $checkoutCharge
     * @param mixed|null $remainingBalanceChargeExactTime
     * @param string|null $remainingBalanceChargeTimeAfterCheckout
     * @param string|null $remainingBalanceChargeTrigger
     */
    public static function make(
        $checkoutCharge = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $remainingBalanceChargeExactTime = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $remainingBalanceChargeTimeAfterCheckout = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $remainingBalanceChargeTrigger = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($checkoutCharge !== self::UNDEFINED) {
            $instance->checkoutCharge = $checkoutCharge;
        }
        if ($remainingBalanceChargeExactTime !== self::UNDEFINED) {
            $instance->remainingBalanceChargeExactTime = $remainingBalanceChargeExactTime;
        }
        if ($remainingBalanceChargeTimeAfterCheckout !== self::UNDEFINED) {
            $instance->remainingBalanceChargeTimeAfterCheckout = $remainingBalanceChargeTimeAfterCheckout;
        }
        if ($remainingBalanceChargeTrigger !== self::UNDEFINED) {
            $instance->remainingBalanceChargeTrigger = $remainingBalanceChargeTrigger;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'checkoutCharge' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SellingPlanCheckoutChargeInput),
            'remainingBalanceChargeExactTime' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'remainingBalanceChargeTimeAfterCheckout' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'remainingBalanceChargeTrigger' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
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
