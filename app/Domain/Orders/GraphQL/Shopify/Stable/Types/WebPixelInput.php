<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property mixed $settings
 */
class WebPixelInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param mixed $settings
     */
    public static function make($settings): self
    {
        $instance = new self;

        if ($settings !== self::UNDEFINED) {
            $instance->settings = $settings;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'settings' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
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
