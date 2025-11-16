<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property array<int|string>|null $conditionsToDelete
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\DeliveryProfileLocationGroupInput>|null $locationGroupsToCreate
 * @property array<int|string>|null $locationGroupsToDelete
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\DeliveryProfileLocationGroupInput>|null $locationGroupsToUpdate
 * @property array<int|string>|null $methodDefinitionsToDelete
 * @property string|null $name
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\DeliveryProfileLocationGroupInput>|null $profileLocationGroups
 * @property array<int|string>|null $sellingPlanGroupsToAssociate
 * @property array<int|string>|null $sellingPlanGroupsToDissociate
 * @property array<int|string>|null $variantsToAssociate
 * @property array<int|string>|null $variantsToDissociate
 * @property array<int|string>|null $zonesToDelete
 */
class DeliveryProfileInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param array<int|string>|null $conditionsToDelete
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\DeliveryProfileLocationGroupInput>|null $locationGroupsToCreate
     * @param array<int|string>|null $locationGroupsToDelete
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\DeliveryProfileLocationGroupInput>|null $locationGroupsToUpdate
     * @param array<int|string>|null $methodDefinitionsToDelete
     * @param string|null $name
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\DeliveryProfileLocationGroupInput>|null $profileLocationGroups
     * @param array<int|string>|null $sellingPlanGroupsToAssociate
     * @param array<int|string>|null $sellingPlanGroupsToDissociate
     * @param array<int|string>|null $variantsToAssociate
     * @param array<int|string>|null $variantsToDissociate
     * @param array<int|string>|null $zonesToDelete
     */
    public static function make(
        $conditionsToDelete = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $locationGroupsToCreate = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $locationGroupsToDelete = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $locationGroupsToUpdate = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $methodDefinitionsToDelete = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $name = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $profileLocationGroups = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $sellingPlanGroupsToAssociate = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $sellingPlanGroupsToDissociate = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $variantsToAssociate = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $variantsToDissociate = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $zonesToDelete = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($conditionsToDelete !== self::UNDEFINED) {
            $instance->conditionsToDelete = $conditionsToDelete;
        }
        if ($locationGroupsToCreate !== self::UNDEFINED) {
            $instance->locationGroupsToCreate = $locationGroupsToCreate;
        }
        if ($locationGroupsToDelete !== self::UNDEFINED) {
            $instance->locationGroupsToDelete = $locationGroupsToDelete;
        }
        if ($locationGroupsToUpdate !== self::UNDEFINED) {
            $instance->locationGroupsToUpdate = $locationGroupsToUpdate;
        }
        if ($methodDefinitionsToDelete !== self::UNDEFINED) {
            $instance->methodDefinitionsToDelete = $methodDefinitionsToDelete;
        }
        if ($name !== self::UNDEFINED) {
            $instance->name = $name;
        }
        if ($profileLocationGroups !== self::UNDEFINED) {
            $instance->profileLocationGroups = $profileLocationGroups;
        }
        if ($sellingPlanGroupsToAssociate !== self::UNDEFINED) {
            $instance->sellingPlanGroupsToAssociate = $sellingPlanGroupsToAssociate;
        }
        if ($sellingPlanGroupsToDissociate !== self::UNDEFINED) {
            $instance->sellingPlanGroupsToDissociate = $sellingPlanGroupsToDissociate;
        }
        if ($variantsToAssociate !== self::UNDEFINED) {
            $instance->variantsToAssociate = $variantsToAssociate;
        }
        if ($variantsToDissociate !== self::UNDEFINED) {
            $instance->variantsToDissociate = $variantsToDissociate;
        }
        if ($zonesToDelete !== self::UNDEFINED) {
            $instance->zonesToDelete = $zonesToDelete;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'conditionsToDelete' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter))),
            'locationGroupsToCreate' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DeliveryProfileLocationGroupInput))),
            'locationGroupsToDelete' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter))),
            'locationGroupsToUpdate' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DeliveryProfileLocationGroupInput))),
            'methodDefinitionsToDelete' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter))),
            'name' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'profileLocationGroups' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DeliveryProfileLocationGroupInput))),
            'sellingPlanGroupsToAssociate' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter))),
            'sellingPlanGroupsToDissociate' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter))),
            'variantsToAssociate' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter))),
            'variantsToDissociate' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter))),
            'zonesToDelete' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter))),
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
