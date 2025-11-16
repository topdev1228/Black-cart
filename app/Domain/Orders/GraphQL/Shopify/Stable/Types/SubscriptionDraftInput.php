<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionBillingPolicyInput|null $billingPolicy
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\AttributeInput>|null $customAttributes
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionDeliveryMethodInput|null $deliveryMethod
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionDeliveryPolicyInput|null $deliveryPolicy
 * @property mixed|null $deliveryPrice
 * @property mixed|null $nextBillingDate
 * @property string|null $note
 * @property int|string|null $paymentMethodId
 * @property string|null $status
 */
class SubscriptionDraftInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionBillingPolicyInput|null $billingPolicy
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\AttributeInput>|null $customAttributes
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionDeliveryMethodInput|null $deliveryMethod
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionDeliveryPolicyInput|null $deliveryPolicy
     * @param mixed|null $deliveryPrice
     * @param mixed|null $nextBillingDate
     * @param string|null $note
     * @param int|string|null $paymentMethodId
     * @param string|null $status
     */
    public static function make(
        $billingPolicy = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $customAttributes = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $deliveryMethod = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $deliveryPolicy = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $deliveryPrice = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $nextBillingDate = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $note = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $paymentMethodId = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $status = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($billingPolicy !== self::UNDEFINED) {
            $instance->billingPolicy = $billingPolicy;
        }
        if ($customAttributes !== self::UNDEFINED) {
            $instance->customAttributes = $customAttributes;
        }
        if ($deliveryMethod !== self::UNDEFINED) {
            $instance->deliveryMethod = $deliveryMethod;
        }
        if ($deliveryPolicy !== self::UNDEFINED) {
            $instance->deliveryPolicy = $deliveryPolicy;
        }
        if ($deliveryPrice !== self::UNDEFINED) {
            $instance->deliveryPrice = $deliveryPrice;
        }
        if ($nextBillingDate !== self::UNDEFINED) {
            $instance->nextBillingDate = $nextBillingDate;
        }
        if ($note !== self::UNDEFINED) {
            $instance->note = $note;
        }
        if ($paymentMethodId !== self::UNDEFINED) {
            $instance->paymentMethodId = $paymentMethodId;
        }
        if ($status !== self::UNDEFINED) {
            $instance->status = $status;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'billingPolicy' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionBillingPolicyInput),
            'customAttributes' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\AttributeInput))),
            'deliveryMethod' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionDeliveryMethodInput),
            'deliveryPolicy' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SubscriptionDeliveryPolicyInput),
            'deliveryPrice' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'nextBillingDate' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'note' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'paymentMethodId' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'status' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
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
