<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\DeliveryCountryInput>|null $countries
 * @property int|string|null $id
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\DeliveryMethodDefinitionInput>|null $methodDefinitionsToCreate
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\DeliveryMethodDefinitionInput>|null $methodDefinitionsToUpdate
 * @property string|null $name
 */
class DeliveryLocationGroupZoneInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\DeliveryCountryInput>|null $countries
     * @param int|string|null $id
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\DeliveryMethodDefinitionInput>|null $methodDefinitionsToCreate
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\DeliveryMethodDefinitionInput>|null $methodDefinitionsToUpdate
     * @param string|null $name
     */
    public static function make(
        $countries = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $id = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $methodDefinitionsToCreate = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $methodDefinitionsToUpdate = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $name = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($countries !== self::UNDEFINED) {
            $instance->countries = $countries;
        }
        if ($id !== self::UNDEFINED) {
            $instance->id = $id;
        }
        if ($methodDefinitionsToCreate !== self::UNDEFINED) {
            $instance->methodDefinitionsToCreate = $methodDefinitionsToCreate;
        }
        if ($methodDefinitionsToUpdate !== self::UNDEFINED) {
            $instance->methodDefinitionsToUpdate = $methodDefinitionsToUpdate;
        }
        if ($name !== self::UNDEFINED) {
            $instance->name = $name;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'countries' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DeliveryCountryInput))),
            'id' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'methodDefinitionsToCreate' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DeliveryMethodDefinitionInput))),
            'methodDefinitionsToUpdate' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DeliveryMethodDefinitionInput))),
            'name' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
