<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $access
 * @property string $grantee
 */
class MetafieldAccessGrantInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $access
     * @param string $grantee
     */
    public static function make($access, $grantee): self
    {
        $instance = new self;

        if ($access !== self::UNDEFINED) {
            $instance->access = $access;
        }
        if ($grantee !== self::UNDEFINED) {
            $instance->grantee = $grantee;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'access' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'grantee' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
