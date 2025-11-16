<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $admin
 * @property array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetafieldAccessGrantOperationInput>|null $grants
 * @property string|null $storefront
 */
class MetafieldAccessUpdateInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $admin
     * @param array<\App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetafieldAccessGrantOperationInput>|null $grants
     * @param string|null $storefront
     */
    public static function make(
        $admin,
        $grants = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $storefront = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($admin !== self::UNDEFINED) {
            $instance->admin = $admin;
        }
        if ($grants !== self::UNDEFINED) {
            $instance->grants = $grants;
        }
        if ($storefront !== self::UNDEFINED) {
            $instance->storefront = $storefront;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'admin' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'grants' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MetafieldAccessGrantOperationInput))),
            'storefront' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
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
