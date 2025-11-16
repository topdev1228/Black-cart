<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property bool $enabled
 */
class MetaobjectCapabilityPublishableInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param bool $enabled
     */
    public static function make($enabled): self
    {
        $instance = new self;

        if ($enabled !== self::UNDEFINED) {
            $instance->enabled = $enabled;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'enabled' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
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
