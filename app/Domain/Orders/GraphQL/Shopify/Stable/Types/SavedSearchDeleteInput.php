<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string $id
 */
class SavedSearchDeleteInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string $id
     */
    public static function make($id): self
    {
        $instance = new self;

        if ($id !== self::UNDEFINED) {
            $instance->id = $id;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'id' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
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
