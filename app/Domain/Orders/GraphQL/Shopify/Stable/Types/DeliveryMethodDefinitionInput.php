<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property bool|null $active
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\DeliveryUpdateConditionInput>|null $conditionsToUpdate
 * @property string|null $description
 * @property int|string|null $id
 * @property string|null $name
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DeliveryParticipantInput|null $participant
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\DeliveryPriceConditionInput>|null $priceConditionsToCreate
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DeliveryRateDefinitionInput|null $rateDefinition
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\DeliveryWeightConditionInput>|null $weightConditionsToCreate
 */
class DeliveryMethodDefinitionInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param bool|null $active
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\DeliveryUpdateConditionInput>|null $conditionsToUpdate
     * @param string|null $description
     * @param int|string|null $id
     * @param string|null $name
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DeliveryParticipantInput|null $participant
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\DeliveryPriceConditionInput>|null $priceConditionsToCreate
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DeliveryRateDefinitionInput|null $rateDefinition
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\DeliveryWeightConditionInput>|null $weightConditionsToCreate
     */
    public static function make(
        $active = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $conditionsToUpdate = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $description = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $id = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $name = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $participant = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $priceConditionsToCreate = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $rateDefinition = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $weightConditionsToCreate = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($active !== self::UNDEFINED) {
            $instance->active = $active;
        }
        if ($conditionsToUpdate !== self::UNDEFINED) {
            $instance->conditionsToUpdate = $conditionsToUpdate;
        }
        if ($description !== self::UNDEFINED) {
            $instance->description = $description;
        }
        if ($id !== self::UNDEFINED) {
            $instance->id = $id;
        }
        if ($name !== self::UNDEFINED) {
            $instance->name = $name;
        }
        if ($participant !== self::UNDEFINED) {
            $instance->participant = $participant;
        }
        if ($priceConditionsToCreate !== self::UNDEFINED) {
            $instance->priceConditionsToCreate = $priceConditionsToCreate;
        }
        if ($rateDefinition !== self::UNDEFINED) {
            $instance->rateDefinition = $rateDefinition;
        }
        if ($weightConditionsToCreate !== self::UNDEFINED) {
            $instance->weightConditionsToCreate = $weightConditionsToCreate;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'active' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'conditionsToUpdate' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DeliveryUpdateConditionInput))),
            'description' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'id' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'name' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'participant' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DeliveryParticipantInput),
            'priceConditionsToCreate' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DeliveryPriceConditionInput))),
            'rateDefinition' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DeliveryRateDefinitionInput),
            'weightConditionsToCreate' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\DeliveryWeightConditionInput))),
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
