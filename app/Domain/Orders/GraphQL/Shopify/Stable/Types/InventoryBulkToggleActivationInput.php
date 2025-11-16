<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property bool $activate
 * @property int|string $locationId
 */
class InventoryBulkToggleActivationInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param bool $activate
     * @param int|string $locationId
     */
    public static function make($activate, $locationId): self
    {
        $instance = new self;

        if ($activate !== self::UNDEFINED) {
            $instance->activate = $activate;
        }
        if ($locationId !== self::UNDEFINED) {
            $instance->locationId = $locationId;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'activate' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'locationId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
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
