<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string|null $customerId
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PurchasingCompanyInput|null $purchasingCompany
 */
class PurchasingEntityInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string|null $customerId
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PurchasingCompanyInput|null $purchasingCompany
     */
    public static function make(
        $customerId = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $purchasingCompany = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($customerId !== self::UNDEFINED) {
            $instance->customerId = $customerId;
        }
        if ($purchasingCompany !== self::UNDEFINED) {
            $instance->purchasingCompany = $purchasingCompany;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'customerId' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'purchasingCompany' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PurchasingCompanyInput),
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
