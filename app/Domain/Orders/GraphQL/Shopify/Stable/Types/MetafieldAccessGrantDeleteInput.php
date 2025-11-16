<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $grantee
 */
class MetafieldAccessGrantDeleteInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $grantee
     */
    public static function make($grantee): self
    {
        $instance = new self;

        if ($grantee !== self::UNDEFINED) {
            $instance->grantee = $grantee;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
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
