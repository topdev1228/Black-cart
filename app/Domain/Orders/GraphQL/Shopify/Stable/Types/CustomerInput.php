<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\MailingAddressInput>|null $addresses
 * @property string|null $email
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CustomerEmailMarketingConsentInput|null $emailMarketingConsent
 * @property string|null $firstName
 * @property int|string|null $id
 * @property string|null $lastName
 * @property string|null $locale
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetafieldInput>|null $metafields
 * @property string|null $note
 * @property string|null $phone
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CustomerSmsMarketingConsentInput|null $smsMarketingConsent
 * @property array<string>|null $tags
 * @property bool|null $taxExempt
 * @property array<string>|null $taxExemptions
 */
class CustomerInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\MailingAddressInput>|null $addresses
     * @param string|null $email
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CustomerEmailMarketingConsentInput|null $emailMarketingConsent
     * @param string|null $firstName
     * @param int|string|null $id
     * @param string|null $lastName
     * @param string|null $locale
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetafieldInput>|null $metafields
     * @param string|null $note
     * @param string|null $phone
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CustomerSmsMarketingConsentInput|null $smsMarketingConsent
     * @param array<string>|null $tags
     * @param bool|null $taxExempt
     * @param array<string>|null $taxExemptions
     */
    public static function make(
        $addresses = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $email = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $emailMarketingConsent = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $firstName = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $id = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $lastName = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $locale = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $metafields = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $note = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $phone = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $smsMarketingConsent = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $tags = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $taxExempt = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $taxExemptions = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($addresses !== self::UNDEFINED) {
            $instance->addresses = $addresses;
        }
        if ($email !== self::UNDEFINED) {
            $instance->email = $email;
        }
        if ($emailMarketingConsent !== self::UNDEFINED) {
            $instance->emailMarketingConsent = $emailMarketingConsent;
        }
        if ($firstName !== self::UNDEFINED) {
            $instance->firstName = $firstName;
        }
        if ($id !== self::UNDEFINED) {
            $instance->id = $id;
        }
        if ($lastName !== self::UNDEFINED) {
            $instance->lastName = $lastName;
        }
        if ($locale !== self::UNDEFINED) {
            $instance->locale = $locale;
        }
        if ($metafields !== self::UNDEFINED) {
            $instance->metafields = $metafields;
        }
        if ($note !== self::UNDEFINED) {
            $instance->note = $note;
        }
        if ($phone !== self::UNDEFINED) {
            $instance->phone = $phone;
        }
        if ($smsMarketingConsent !== self::UNDEFINED) {
            $instance->smsMarketingConsent = $smsMarketingConsent;
        }
        if ($tags !== self::UNDEFINED) {
            $instance->tags = $tags;
        }
        if ($taxExempt !== self::UNDEFINED) {
            $instance->taxExempt = $taxExempt;
        }
        if ($taxExemptions !== self::UNDEFINED) {
            $instance->taxExemptions = $taxExemptions;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'addresses' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MailingAddressInput))),
            'email' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'emailMarketingConsent' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CustomerEmailMarketingConsentInput),
            'firstName' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'id' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'lastName' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'locale' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'metafields' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetafieldInput))),
            'note' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'phone' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'smsMarketingConsent' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\CustomerSmsMarketingConsentInput),
            'tags' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter))),
            'taxExempt' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'taxExemptions' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\EnumConverter))),
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
