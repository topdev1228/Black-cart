<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectFieldDefinitionCreateInput|null $create
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectFieldDefinitionDeleteInput|null $delete
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectFieldDefinitionUpdateInput|null $update
 */
class MetaobjectFieldDefinitionOperationInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectFieldDefinitionCreateInput|null $create
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectFieldDefinitionDeleteInput|null $delete
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectFieldDefinitionUpdateInput|null $update
     */
    public static function make(
        $create = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $delete = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $update = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($create !== self::UNDEFINED) {
            $instance->create = $create;
        }
        if ($delete !== self::UNDEFINED) {
            $instance->delete = $delete;
        }
        if ($update !== self::UNDEFINED) {
            $instance->update = $update;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'create' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectFieldDefinitionCreateInput),
            'delete' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectFieldDefinitionDeleteInput),
            'update' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetaobjectFieldDefinitionUpdateInput),
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
