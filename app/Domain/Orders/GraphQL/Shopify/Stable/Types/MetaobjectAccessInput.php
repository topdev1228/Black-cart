<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string|null $admin
 * @property string|null $storefront
 */
class MetaobjectAccessInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string|null $admin
     * @param string|null $storefront
     */
    public static function make(
        $admin = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $storefront = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($admin !== self::UNDEFINED) {
            $instance->admin = $admin;
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
            'admin' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
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
