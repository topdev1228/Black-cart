<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property array<int|string>|null $customerIdsToAdd
 * @property array<int|string>|null $customerIdsToRemove
 * @property bool|null $forAllCustomers
 * @property array<int|string>|null $segmentIds
 */
class PriceRuleCustomerSelectionInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param array<int|string>|null $customerIdsToAdd
     * @param array<int|string>|null $customerIdsToRemove
     * @param bool|null $forAllCustomers
     * @param array<int|string>|null $segmentIds
     */
    public static function make(
        $customerIdsToAdd = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $customerIdsToRemove = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $forAllCustomers = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $segmentIds = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($customerIdsToAdd !== self::UNDEFINED) {
            $instance->customerIdsToAdd = $customerIdsToAdd;
        }
        if ($customerIdsToRemove !== self::UNDEFINED) {
            $instance->customerIdsToRemove = $customerIdsToRemove;
        }
        if ($forAllCustomers !== self::UNDEFINED) {
            $instance->forAllCustomers = $forAllCustomers;
        }
        if ($segmentIds !== self::UNDEFINED) {
            $instance->segmentIds = $segmentIds;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'customerIdsToAdd' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter))),
            'customerIdsToRemove' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter))),
            'forAllCustomers' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'segmentIds' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter))),
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
