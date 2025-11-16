<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string|null $font
 * @property string|null $kerning
 * @property string|null $letterCase
 * @property string|null $size
 * @property string|null $weight
 */
class CheckoutBrandingTypographyStyleInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string|null $font
     * @param string|null $kerning
     * @param string|null $letterCase
     * @param string|null $size
     * @param string|null $weight
     */
    public static function make(
        $font = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $kerning = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $letterCase = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $size = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $weight = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($font !== self::UNDEFINED) {
            $instance->font = $font;
        }
        if ($kerning !== self::UNDEFINED) {
            $instance->kerning = $kerning;
        }
        if ($letterCase !== self::UNDEFINED) {
            $instance->letterCase = $letterCase;
        }
        if ($size !== self::UNDEFINED) {
            $instance->size = $size;
        }
        if ($weight !== self::UNDEFINED) {
            $instance->weight = $weight;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'font' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'kerning' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'letterCase' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'size' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'weight' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
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
