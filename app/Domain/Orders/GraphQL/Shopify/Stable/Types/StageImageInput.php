<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $filename
 * @property string $mimeType
 * @property string $resource
 * @property string|null $httpMethod
 */
class StageImageInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $filename
     * @param string $mimeType
     * @param string $resource
     * @param string|null $httpMethod
     */
    public static function make(
        $filename,
        $mimeType,
        $resource,
        $httpMethod = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($filename !== self::UNDEFINED) {
            $instance->filename = $filename;
        }
        if ($mimeType !== self::UNDEFINED) {
            $instance->mimeType = $mimeType;
        }
        if ($resource !== self::UNDEFINED) {
            $instance->resource = $resource;
        }
        if ($httpMethod !== self::UNDEFINED) {
            $instance->httpMethod = $httpMethod;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'filename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'mimeType' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'resource' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'httpMethod' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
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
