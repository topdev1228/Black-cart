<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string|null $path
 * @property string|null $target
 */
class UrlRedirectInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string|null $path
     * @param string|null $target
     */
    public static function make(
        $path = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $target = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($path !== self::UNDEFINED) {
            $instance->path = $path;
        }
        if ($target !== self::UNDEFINED) {
            $instance->target = $target;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'path' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'target' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
