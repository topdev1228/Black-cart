<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $originalSource
 * @property string|null $alt
 * @property string|null $contentType
 * @property string|null $duplicateResolutionMode
 * @property string|null $filename
 */
class FileCreateInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $originalSource
     * @param string|null $alt
     * @param string|null $contentType
     * @param string|null $duplicateResolutionMode
     * @param string|null $filename
     */
    public static function make(
        $originalSource,
        $alt = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $contentType = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $duplicateResolutionMode = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $filename = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($originalSource !== self::UNDEFINED) {
            $instance->originalSource = $originalSource;
        }
        if ($alt !== self::UNDEFINED) {
            $instance->alt = $alt;
        }
        if ($contentType !== self::UNDEFINED) {
            $instance->contentType = $contentType;
        }
        if ($duplicateResolutionMode !== self::UNDEFINED) {
            $instance->duplicateResolutionMode = $duplicateResolutionMode;
        }
        if ($filename !== self::UNDEFINED) {
            $instance->filename = $filename;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'originalSource' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'alt' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'contentType' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'duplicateResolutionMode' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'filename' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
