<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string $id
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\AttributeInput>|null $customAttributes
 * @property string|null $email
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\LocalizationExtensionInput>|null $localizationExtensions
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetafieldInput>|null $metafields
 * @property string|null $note
 * @property string|null $poNumber
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MailingAddressInput|null $shippingAddress
 * @property array<string>|null $tags
 */
class OrderInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string $id
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\AttributeInput>|null $customAttributes
     * @param string|null $email
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\LocalizationExtensionInput>|null $localizationExtensions
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetafieldInput>|null $metafields
     * @param string|null $note
     * @param string|null $poNumber
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MailingAddressInput|null $shippingAddress
     * @param array<string>|null $tags
     */
    public static function make(
        $id,
        $customAttributes = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $email = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $localizationExtensions = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $metafields = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $note = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $poNumber = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $shippingAddress = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $tags = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($id !== self::UNDEFINED) {
            $instance->id = $id;
        }
        if ($customAttributes !== self::UNDEFINED) {
            $instance->customAttributes = $customAttributes;
        }
        if ($email !== self::UNDEFINED) {
            $instance->email = $email;
        }
        if ($localizationExtensions !== self::UNDEFINED) {
            $instance->localizationExtensions = $localizationExtensions;
        }
        if ($metafields !== self::UNDEFINED) {
            $instance->metafields = $metafields;
        }
        if ($note !== self::UNDEFINED) {
            $instance->note = $note;
        }
        if ($poNumber !== self::UNDEFINED) {
            $instance->poNumber = $poNumber;
        }
        if ($shippingAddress !== self::UNDEFINED) {
            $instance->shippingAddress = $shippingAddress;
        }
        if ($tags !== self::UNDEFINED) {
            $instance->tags = $tags;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'id' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'customAttributes' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\AttributeInput))),
            'email' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'localizationExtensions' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\LocalizationExtensionInput))),
            'metafields' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetafieldInput))),
            'note' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'poNumber' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'shippingAddress' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MailingAddressInput),
            'tags' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter))),
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
