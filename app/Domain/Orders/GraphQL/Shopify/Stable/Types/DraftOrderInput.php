<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DraftOrderAppliedDiscountInput|null $appliedDiscount
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MailingAddressInput|null $billingAddress
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\AttributeInput>|null $customAttributes
 * @property string|null $email
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\DraftOrderLineItemInput>|null $lineItems
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\LocalizationExtensionInput>|null $localizationExtensions
 * @property string|null $marketRegionCountryCode
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetafieldInput>|null $metafields
 * @property string|null $note
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PaymentTermsInput|null $paymentTerms
 * @property string|null $phone
 * @property string|null $poNumber
 * @property string|null $presentmentCurrencyCode
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PurchasingEntityInput|null $purchasingEntity
 * @property mixed|null $reserveInventoryUntil
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MailingAddressInput|null $shippingAddress
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ShippingLineInput|null $shippingLine
 * @property string|null $sourceName
 * @property array<string>|null $tags
 * @property bool|null $taxExempt
 * @property bool|null $useCustomerDefaultAddress
 * @property bool|null $visibleToCustomer
 */
class DraftOrderInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DraftOrderAppliedDiscountInput|null $appliedDiscount
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MailingAddressInput|null $billingAddress
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\AttributeInput>|null $customAttributes
     * @param string|null $email
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\DraftOrderLineItemInput>|null $lineItems
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\LocalizationExtensionInput>|null $localizationExtensions
     * @param string|null $marketRegionCountryCode
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetafieldInput>|null $metafields
     * @param string|null $note
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PaymentTermsInput|null $paymentTerms
     * @param string|null $phone
     * @param string|null $poNumber
     * @param string|null $presentmentCurrencyCode
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PurchasingEntityInput|null $purchasingEntity
     * @param mixed|null $reserveInventoryUntil
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MailingAddressInput|null $shippingAddress
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ShippingLineInput|null $shippingLine
     * @param string|null $sourceName
     * @param array<string>|null $tags
     * @param bool|null $taxExempt
     * @param bool|null $useCustomerDefaultAddress
     * @param bool|null $visibleToCustomer
     */
    public static function make(
        $appliedDiscount = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $billingAddress = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $customAttributes = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $email = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $lineItems = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $localizationExtensions = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $marketRegionCountryCode = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $metafields = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $note = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $paymentTerms = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $phone = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $poNumber = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $presentmentCurrencyCode = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $purchasingEntity = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $reserveInventoryUntil = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $shippingAddress = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $shippingLine = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $sourceName = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $tags = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $taxExempt = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $useCustomerDefaultAddress = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $visibleToCustomer = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($appliedDiscount !== self::UNDEFINED) {
            $instance->appliedDiscount = $appliedDiscount;
        }
        if ($billingAddress !== self::UNDEFINED) {
            $instance->billingAddress = $billingAddress;
        }
        if ($customAttributes !== self::UNDEFINED) {
            $instance->customAttributes = $customAttributes;
        }
        if ($email !== self::UNDEFINED) {
            $instance->email = $email;
        }
        if ($lineItems !== self::UNDEFINED) {
            $instance->lineItems = $lineItems;
        }
        if ($localizationExtensions !== self::UNDEFINED) {
            $instance->localizationExtensions = $localizationExtensions;
        }
        if ($marketRegionCountryCode !== self::UNDEFINED) {
            $instance->marketRegionCountryCode = $marketRegionCountryCode;
        }
        if ($metafields !== self::UNDEFINED) {
            $instance->metafields = $metafields;
        }
        if ($note !== self::UNDEFINED) {
            $instance->note = $note;
        }
        if ($paymentTerms !== self::UNDEFINED) {
            $instance->paymentTerms = $paymentTerms;
        }
        if ($phone !== self::UNDEFINED) {
            $instance->phone = $phone;
        }
        if ($poNumber !== self::UNDEFINED) {
            $instance->poNumber = $poNumber;
        }
        if ($presentmentCurrencyCode !== self::UNDEFINED) {
            $instance->presentmentCurrencyCode = $presentmentCurrencyCode;
        }
        if ($purchasingEntity !== self::UNDEFINED) {
            $instance->purchasingEntity = $purchasingEntity;
        }
        if ($reserveInventoryUntil !== self::UNDEFINED) {
            $instance->reserveInventoryUntil = $reserveInventoryUntil;
        }
        if ($shippingAddress !== self::UNDEFINED) {
            $instance->shippingAddress = $shippingAddress;
        }
        if ($shippingLine !== self::UNDEFINED) {
            $instance->shippingLine = $shippingLine;
        }
        if ($sourceName !== self::UNDEFINED) {
            $instance->sourceName = $sourceName;
        }
        if ($tags !== self::UNDEFINED) {
            $instance->tags = $tags;
        }
        if ($taxExempt !== self::UNDEFINED) {
            $instance->taxExempt = $taxExempt;
        }
        if ($useCustomerDefaultAddress !== self::UNDEFINED) {
            $instance->useCustomerDefaultAddress = $useCustomerDefaultAddress;
        }
        if ($visibleToCustomer !== self::UNDEFINED) {
            $instance->visibleToCustomer = $visibleToCustomer;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'appliedDiscount' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DraftOrderAppliedDiscountInput),
            'billingAddress' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MailingAddressInput),
            'customAttributes' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\AttributeInput))),
            'email' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'lineItems' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DraftOrderLineItemInput))),
            'localizationExtensions' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\LocalizationExtensionInput))),
            'marketRegionCountryCode' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'metafields' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetafieldInput))),
            'note' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'paymentTerms' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PaymentTermsInput),
            'phone' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'poNumber' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'presentmentCurrencyCode' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'purchasingEntity' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\PurchasingEntityInput),
            'reserveInventoryUntil' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'shippingAddress' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MailingAddressInput),
            'shippingLine' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\ShippingLineInput),
            'sourceName' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'tags' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter))),
            'taxExempt' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'useCustomerDefaultAddress' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'visibleToCustomer' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
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
