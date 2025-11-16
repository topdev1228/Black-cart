<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property bool|null $appliesOncePerCustomer
 * @property string|null $code
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountCombinesWithInput|null $combinesWith
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountCustomerSelectionInput|null $customerSelection
 * @property mixed|null $endsAt
 * @property string|null $functionId
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetafieldInput>|null $metafields
 * @property mixed|null $startsAt
 * @property string|null $title
 * @property int|null $usageLimit
 */
class DiscountCodeAppInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param bool|null $appliesOncePerCustomer
     * @param string|null $code
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountCombinesWithInput|null $combinesWith
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountCustomerSelectionInput|null $customerSelection
     * @param mixed|null $endsAt
     * @param string|null $functionId
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetafieldInput>|null $metafields
     * @param mixed|null $startsAt
     * @param string|null $title
     * @param int|null $usageLimit
     */
    public static function make(
        $appliesOncePerCustomer = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $code = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $combinesWith = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $customerSelection = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $endsAt = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $functionId = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $metafields = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $startsAt = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $title = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $usageLimit = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
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
        if ($customerSelection !== self::UNDEFINED) {
            $instance->customerSelection = $customerSelection;
        }
        if ($endsAt !== self::UNDEFINED) {
            $instance->endsAt = $endsAt;
        }
        if ($functionId !== self::UNDEFINED) {
            $instance->functionId = $functionId;
        }
        if ($metafields !== self::UNDEFINED) {
            $instance->metafields = $metafields;
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

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'appliesOncePerCustomer' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'code' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'combinesWith' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountCombinesWithInput),
            'customerSelection' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountCustomerSelectionInput),
            'endsAt' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'functionId' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'metafields' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetafieldInput))),
            'startsAt' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'title' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'usageLimit' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
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
