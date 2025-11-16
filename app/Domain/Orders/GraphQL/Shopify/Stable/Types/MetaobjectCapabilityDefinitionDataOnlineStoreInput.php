<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $urlHandle
 * @property bool|null $createRedirects
 */
class MetaobjectCapabilityDefinitionDataOnlineStoreInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $urlHandle
     * @param bool|null $createRedirects
     */
    public static function make(
        $urlHandle,
        $createRedirects = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($urlHandle !== self::UNDEFINED) {
            $instance->urlHandle = $urlHandle;
        }
        if ($createRedirects !== self::UNDEFINED) {
            $instance->createRedirects = $createRedirects;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'urlHandle' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'createRedirects' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
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
