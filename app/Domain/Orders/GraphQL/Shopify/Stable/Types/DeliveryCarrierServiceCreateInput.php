<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property bool $active
 * @property mixed $callbackUrl
 * @property string $name
 * @property bool $supportsServiceDiscovery
 */
class DeliveryCarrierServiceCreateInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param bool $active
     * @param mixed $callbackUrl
     * @param string $name
     * @param bool $supportsServiceDiscovery
     */
    public static function make($active, $callbackUrl, $name, $supportsServiceDiscovery): self
    {
        $instance = new self;

        if ($active !== self::UNDEFINED) {
            $instance->active = $active;
        }
        if ($callbackUrl !== self::UNDEFINED) {
            $instance->callbackUrl = $callbackUrl;
        }
        if ($name !== self::UNDEFINED) {
            $instance->name = $name;
        }
        if ($supportsServiceDiscovery !== self::UNDEFINED) {
            $instance->supportsServiceDiscovery = $supportsServiceDiscovery;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'active' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'callbackUrl' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'name' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'supportsServiceDiscovery' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
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
