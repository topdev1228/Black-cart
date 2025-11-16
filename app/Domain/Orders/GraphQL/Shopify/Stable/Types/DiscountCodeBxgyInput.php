<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property bool|null $appliesOncePerCustomer
 * @property string|null $code
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountCombinesWithInput|null $combinesWith
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountCustomerBuysInput|null $customerBuys
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountCustomerGetsInput|null $customerGets
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountCustomerSelectionInput|null $customerSelection
 * @property mixed|null $endsAt
 * @property mixed|null $startsAt
 * @property string|null $title
 * @property int|null $usageLimit
 * @property int|null $usesPerOrderLimit
 */
class DiscountCodeBxgyInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param bool|null $appliesOncePerCustomer
     * @param string|null $code
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountCombinesWithInput|null $combinesWith
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountCustomerBuysInput|null $customerBuys
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountCustomerGetsInput|null $customerGets
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountCustomerSelectionInput|null $customerSelection
     * @param mixed|null $endsAt
     * @param mixed|null $startsAt
     * @param string|null $title
     * @param int|null $usageLimit
     * @param int|null $usesPerOrderLimit
     */
    public static function make(
        $appliesOncePerCustomer = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $code = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $combinesWith = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $customerBuys = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $customerGets = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $customerSelection = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $endsAt = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $startsAt = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $title = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $usageLimit = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $usesPerOrderLimit = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($appliesOncePerCustomer !== self::UNDEFINED) {
            $instance->appliesOncePerCustomer = $appliesOncePerCustomer;
        }
        if ($code !== self::UNDEFINED) {
            $instance->code = $code;
        }
        if ($combinesWith !== self::UNDEFINED) {
            $instance->combinesWith = $combinesWith;
        }
        if ($customerBuys !== self::UNDEFINED) {
            $instance->customerBuys = $customerBuys;
        }
        if ($customerGets !== self::UNDEFINED) {
            $instance->customerGets = $customerGets;
        }
        if ($customerSelection !== self::UNDEFINED) {
            $instance->customerSelection = $customerSelection;
        }
        if ($endsAt !== self::UNDEFINED) {
            $instance->endsAt = $endsAt;
        }
        if ($startsAt !== self::UNDEFINED) {
            $instance->startsAt = $startsAt;
        }
        if ($title !== self::UNDEFINED) {
            $instance->title = $title;
        }
        if ($usageLimit !== self::UNDEFINED) {
            $instance->usageLimit = $usageLimit;
        }
        if ($usesPerOrderLimit !== self::UNDEFINED) {
            $instance->usesPerOrderLimit = $usesPerOrderLimit;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'appliesOncePerCustomer' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'code' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'combinesWith' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountCombinesWithInput),
            'customerBuys' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountCustomerBuysInput),
            'customerGets' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountCustomerGetsInput),
            'customerSelection' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountCustomerSelectionInput),
            'endsAt' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'startsAt' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'title' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'usageLimit' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'usesPerOrderLimit' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
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
