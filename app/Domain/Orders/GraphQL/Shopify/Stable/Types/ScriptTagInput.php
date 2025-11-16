<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property bool|null $cache
 * @property string|null $displayScope
 * @property mixed|null $src
 */
class ScriptTagInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param bool|null $cache
     * @param string|null $displayScope
     * @param mixed|null $src
     */
    public static function make(
        $cache = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $displayScope = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $src = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($cache !== self::UNDEFINED) {
            $instance->cache = $cache;
        }
        if ($displayScope !== self::UNDEFINED) {
            $instance->displayScope = $displayScope;
        }
        if ($src !== self::UNDEFINED) {
            $instance->src = $src;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'cache' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'displayScope' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'src' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
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
