<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CompanyInput $company
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CompanyContactInput|null $companyContact
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CompanyLocationInput|null $companyLocation
 */
class CompanyCreateInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CompanyInput $company
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CompanyContactInput|null $companyContact
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CompanyLocationInput|null $companyLocation
     */
    public static function make(
        $company,
        $companyContact = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $companyLocation = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($company !== self::UNDEFINED) {
            $instance->company = $company;
        }
        if ($companyContact !== self::UNDEFINED) {
            $instance->companyContact = $companyContact;
        }
        if ($companyLocation !== self::UNDEFINED) {
            $instance->companyLocation = $companyLocation;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'company' => new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CompanyInput),
            'companyContact' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CompanyContactInput),
            'companyLocation' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CompanyLocationInput),
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
