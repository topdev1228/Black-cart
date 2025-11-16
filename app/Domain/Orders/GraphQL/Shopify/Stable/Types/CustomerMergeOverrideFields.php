<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string|null $customerIdOfDefaultAddressToKeep
 * @property int|string|null $customerIdOfEmailToKeep
 * @property int|string|null $customerIdOfFirstNameToKeep
 * @property int|string|null $customerIdOfLastNameToKeep
 * @property int|string|null $customerIdOfPhoneNumberToKeep
 * @property string|null $note
 * @property array<string>|null $tags
 */
class CustomerMergeOverrideFields extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string|null $customerIdOfDefaultAddressToKeep
     * @param int|string|null $customerIdOfEmailToKeep
     * @param int|string|null $customerIdOfFirstNameToKeep
     * @param int|string|null $customerIdOfLastNameToKeep
     * @param int|string|null $customerIdOfPhoneNumberToKeep
     * @param string|null $note
     * @param array<string>|null $tags
     */
    public static function make(
        $customerIdOfDefaultAddressToKeep = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $customerIdOfEmailToKeep = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $customerIdOfFirstNameToKeep = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $customerIdOfLastNameToKeep = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $customerIdOfPhoneNumberToKeep = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $note = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $tags = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($customerIdOfDefaultAddressToKeep !== self::UNDEFINED) {
            $instance->customerIdOfDefaultAddressToKeep = $customerIdOfDefaultAddressToKeep;
        }
        if ($customerIdOfEmailToKeep !== self::UNDEFINED) {
            $instance->customerIdOfEmailToKeep = $customerIdOfEmailToKeep;
        }
        if ($customerIdOfFirstNameToKeep !== self::UNDEFINED) {
            $instance->customerIdOfFirstNameToKeep = $customerIdOfFirstNameToKeep;
        }
        if ($customerIdOfLastNameToKeep !== self::UNDEFINED) {
            $instance->customerIdOfLastNameToKeep = $customerIdOfLastNameToKeep;
        }
        if ($customerIdOfPhoneNumberToKeep !== self::UNDEFINED) {
            $instance->customerIdOfPhoneNumberToKeep = $customerIdOfPhoneNumberToKeep;
        }
        if ($note !== self::UNDEFINED) {
            $instance->note = $note;
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
            'customerIdOfDefaultAddressToKeep' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'customerIdOfEmailToKeep' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'customerIdOfFirstNameToKeep' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'customerIdOfLastNameToKeep' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'customerIdOfPhoneNumberToKeep' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'note' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
