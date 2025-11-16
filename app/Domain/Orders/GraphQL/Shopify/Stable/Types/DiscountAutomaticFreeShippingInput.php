<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property bool|null $appliesOnOneTimePurchase
 * @property bool|null $appliesOnSubscription
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountCombinesWithInput|null $combinesWith
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountShippingDestinationSelectionInput|null $destination
 * @property mixed|null $endsAt
 * @property mixed|null $maximumShippingPrice
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountMinimumRequirementInput|null $minimumRequirement
 * @property int|null $recurringCycleLimit
 * @property mixed|null $startsAt
 * @property string|null $title
 */
class DiscountAutomaticFreeShippingInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param bool|null $appliesOnOneTimePurchase
     * @param bool|null $appliesOnSubscription
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountCombinesWithInput|null $combinesWith
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountShippingDestinationSelectionInput|null $destination
     * @param mixed|null $endsAt
     * @param mixed|null $maximumShippingPrice
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountMinimumRequirementInput|null $minimumRequirement
     * @param int|null $recurringCycleLimit
     * @param mixed|null $startsAt
     * @param string|null $title
     */
    public static function make(
        $appliesOnOneTimePurchase = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $appliesOnSubscription = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $combinesWith = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $destination = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $endsAt = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $maximumShippingPrice = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $minimumRequirement = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $recurringCycleLimit = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $startsAt = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $title = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($appliesOnOneTimePurchase !== self::UNDEFINED) {
            $instance->appliesOnOneTimePurchase = $appliesOnOneTimePurchase;
        }
        if ($appliesOnSubscription !== self::UNDEFINED) {
            $instance->appliesOnSubscription = $appliesOnSubscription;
        }
        if ($combinesWith !== self::UNDEFINED) {
            $instance->combinesWith = $combinesWith;
        }
        if ($destination !== self::UNDEFINED) {
            $instance->destination = $destination;
        }
        if ($endsAt !== self::UNDEFINED) {
            $instance->endsAt = $endsAt;
        }
        if ($maximumShippingPrice !== self::UNDEFINED) {
            $instance->maximumShippingPrice = $maximumShippingPrice;
        }
        if ($minimumRequirement !== self::UNDEFINED) {
            $instance->minimumRequirement = $minimumRequirement;
        }
        if ($recurringCycleLimit !== self::UNDEFINED) {
            $instance->recurringCycleLimit = $recurringCycleLimit;
        }
        if ($startsAt !== self::UNDEFINED) {
            $instance->startsAt = $startsAt;
        }
        if ($title !== self::UNDEFINED) {
            $instance->title = $title;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'appliesOnOneTimePurchase' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'appliesOnSubscription' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'combinesWith' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountCombinesWithInput),
            'destination' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountShippingDestinationSelectionInput),
            'endsAt' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'maximumShippingPrice' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'minimumRequirement' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountMinimumRequirementInput),
            'recurringCycleLimit' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'startsAt' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'title' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
