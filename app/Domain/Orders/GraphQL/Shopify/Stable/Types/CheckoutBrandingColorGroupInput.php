<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string|null $accent
 * @property string|null $background
 * @property string|null $foreground
 */
class CheckoutBrandingColorGroupInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string|null $accent
     * @param string|null $background
     * @param string|null $foreground
     */
    public static function make(
        $accent = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $background = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $foreground = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($accent !== self::UNDEFINED) {
            $instance->accent = $accent;
        }
        if ($background !== self::UNDEFINED) {
            $instance->background = $background;
        }
        if ($foreground !== self::UNDEFINED) {
            $instance->foreground = $foreground;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'accent' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'background' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'foreground' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
