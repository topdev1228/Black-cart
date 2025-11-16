<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $handle
 * @property string $type
 */
class MetaobjectHandleInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $handle
     * @param string $type
     */
    public static function make($handle, $type): self
    {
        $instance = new self;

        if ($handle !== self::UNDEFINED) {
            $instance->handle = $handle;
        }
        if ($type !== self::UNDEFINED) {
            $instance->type = $type;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'handle' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'type' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
