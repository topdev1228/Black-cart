<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string|null $alt
 * @property string|null $mediaContentType
 * @property string|null $originalSource
 */
class CreateMediaInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string|null $alt
     * @param string|null $mediaContentType
     * @param string|null $originalSource
     */
    public static function make(
        $alt = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $mediaContentType = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $originalSource = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($alt !== self::UNDEFINED) {
            $instance->alt = $alt;
        }
        if ($mediaContentType !== self::UNDEFINED) {
            $instance->mediaContentType = $mediaContentType;
        }
        if ($originalSource !== self::UNDEFINED) {
            $instance->originalSource = $originalSource;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'alt' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'mediaContentType' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'originalSource' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
