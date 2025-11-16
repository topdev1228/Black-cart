<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string|null $appId
 * @property string|null $description
 * @property string|null $merchantCode
 * @property string|null $name
 * @property array<string>|null $options
 * @property int|null $position
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\SellingPlanInput>|null $sellingPlansToCreate
 * @property array<int|string>|null $sellingPlansToDelete
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\SellingPlanInput>|null $sellingPlansToUpdate
 */
class SellingPlanGroupInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string|null $appId
     * @param string|null $description
     * @param string|null $merchantCode
     * @param string|null $name
     * @param array<string>|null $options
     * @param int|null $position
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\SellingPlanInput>|null $sellingPlansToCreate
     * @param array<int|string>|null $sellingPlansToDelete
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\SellingPlanInput>|null $sellingPlansToUpdate
     */
    public static function make(
        $appId = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $description = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $merchantCode = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $name = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $options = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $position = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $sellingPlansToCreate = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $sellingPlansToDelete = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $sellingPlansToUpdate = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($appId !== self::UNDEFINED) {
            $instance->appId = $appId;
        }
        if ($description !== self::UNDEFINED) {
            $instance->description = $description;
        }
        if ($merchantCode !== self::UNDEFINED) {
            $instance->merchantCode = $merchantCode;
        }
        if ($name !== self::UNDEFINED) {
            $instance->name = $name;
        }
        if ($options !== self::UNDEFINED) {
            $instance->options = $options;
        }
        if ($position !== self::UNDEFINED) {
            $instance->position = $position;
        }
        if ($sellingPlansToCreate !== self::UNDEFINED) {
            $instance->sellingPlansToCreate = $sellingPlansToCreate;
        }
        if ($sellingPlansToDelete !== self::UNDEFINED) {
            $instance->sellingPlansToDelete = $sellingPlansToDelete;
        }
        if ($sellingPlansToUpdate !== self::UNDEFINED) {
            $instance->sellingPlansToUpdate = $sellingPlansToUpdate;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'appId' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'description' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'merchantCode' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'name' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'options' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter))),
            'position' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'sellingPlansToCreate' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SellingPlanInput))),
            'sellingPlansToDelete' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter))),
            'sellingPlansToUpdate' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SellingPlanInput))),
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
