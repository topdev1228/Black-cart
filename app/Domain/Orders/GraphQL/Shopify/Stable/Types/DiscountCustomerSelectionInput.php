<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property bool|null $all
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountCustomerSegmentsInput|null $customerSegments
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountCustomersInput|null $customers
 */
class DiscountCustomerSelectionInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param bool|null $all
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountCustomerSegmentsInput|null $customerSegments
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountCustomersInput|null $customers
     */
    public static function make(
        $all = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $customerSegments = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $customers = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($all !== self::UNDEFINED) {
            $instance->all = $all;
        }
        if ($customerSegments !== self::UNDEFINED) {
            $instance->customerSegments = $customerSegments;
        }
        if ($customers !== self::UNDEFINED) {
            $instance->customers = $customers;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'all' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'customerSegments' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountCustomerSegmentsInput),
            'customers' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DiscountCustomersInput),
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
