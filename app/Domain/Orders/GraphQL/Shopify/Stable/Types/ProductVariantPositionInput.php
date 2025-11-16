<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string $id
 * @property int $position
 */
class ProductVariantPositionInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string $id
     * @param int $position
     */
    public static function make($id, $position): self
    {
        $instance = new self;

        if ($id !== self::UNDEFINED) {
            $instance->id = $id;
        }
        if ($position !== self::UNDEFINED) {
            $instance->position = $position;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'id' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'position' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IntConverter),
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
