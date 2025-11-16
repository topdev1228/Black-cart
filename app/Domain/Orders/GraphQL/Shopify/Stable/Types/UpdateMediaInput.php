<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string $id
 * @property string|null $alt
 * @property string|null $previewImageSource
 */
class UpdateMediaInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string $id
     * @param string|null $alt
     * @param string|null $previewImageSource
     */
    public static function make(
        $id,
        $alt = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $previewImageSource = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($id !== self::UNDEFINED) {
            $instance->id = $id;
        }
        if ($alt !== self::UNDEFINED) {
            $instance->alt = $alt;
        }
        if ($previewImageSource !== self::UNDEFINED) {
            $instance->previewImageSource = $previewImageSource;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'id' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'alt' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'previewImageSource' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
