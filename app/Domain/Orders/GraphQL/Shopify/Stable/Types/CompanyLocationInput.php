<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CompanyAddressInput|null $billingAddress
 * @property bool|null $billingSameAsShipping
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\BuyerExperienceConfigurationInput|null $buyerExperienceConfiguration
 * @property string|null $externalId
 * @property string|null $locale
 * @property string|null $name
 * @property string|null $note
 * @property string|null $phone
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CompanyAddressInput|null $shippingAddress
 * @property array<string>|null $taxExemptions
 * @property string|null $taxRegistrationId
 */
class CompanyLocationInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CompanyAddressInput|null $billingAddress
     * @param bool|null $billingSameAsShipping
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\BuyerExperienceConfigurationInput|null $buyerExperienceConfiguration
     * @param string|null $externalId
     * @param string|null $locale
     * @param string|null $name
     * @param string|null $note
     * @param string|null $phone
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CompanyAddressInput|null $shippingAddress
     * @param array<string>|null $taxExemptions
     * @param string|null $taxRegistrationId
     */
    public static function make(
        $billingAddress = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $billingSameAsShipping = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $buyerExperienceConfiguration = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $externalId = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $locale = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $name = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $note = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $phone = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $shippingAddress = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $taxExemptions = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $taxRegistrationId = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($billingAddress !== self::UNDEFINED) {
            $instance->billingAddress = $billingAddress;
        }
        if ($billingSameAsShipping !== self::UNDEFINED) {
            $instance->billingSameAsShipping = $billingSameAsShipping;
        }
        if ($buyerExperienceConfiguration !== self::UNDEFINED) {
            $instance->buyerExperienceConfiguration = $buyerExperienceConfiguration;
        }
        if ($externalId !== self::UNDEFINED) {
            $instance->externalId = $externalId;
        }
        if ($locale !== self::UNDEFINED) {
            $instance->locale = $locale;
        }
        if ($name !== self::UNDEFINED) {
            $instance->name = $name;
        }
        if ($note !== self::UNDEFINED) {
            $instance->note = $note;
        }
        if ($phone !== self::UNDEFINED) {
            $instance->phone = $phone;
        }
        if ($shippingAddress !== self::UNDEFINED) {
            $instance->shippingAddress = $shippingAddress;
        }
        if ($taxExemptions !== self::UNDEFINED) {
            $instance->taxExemptions = $taxExemptions;
        }
        if ($taxRegistrationId !== self::UNDEFINED) {
            $instance->taxRegistrationId = $taxRegistrationId;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'billingAddress' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CompanyAddressInput),
            'billingSameAsShipping' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'buyerExperienceConfiguration' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\BuyerExperienceConfigurationInput),
            'externalId' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'locale' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'name' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'note' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'phone' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'shippingAddress' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CompanyAddressInput),
            'taxExemptions' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\EnumConverter))),
            'taxRegistrationId' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
