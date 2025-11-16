<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string|null $accent
 * @property string|null $brand
 * @property string|null $critical
 * @property string|null $decorative
 * @property string|null $info
 * @property string|null $success
 * @property string|null $warning
 */
class CheckoutBrandingColorGlobalInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string|null $accent
     * @param string|null $brand
     * @param string|null $critical
     * @param string|null $decorative
     * @param string|null $info
     * @param string|null $success
     * @param string|null $warning
     */
    public static function make(
        $accent = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $brand = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $critical = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $decorative = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $info = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $success = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $warning = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($accent !== self::UNDEFINED) {
            $instance->accent = $accent;
        }
        if ($brand !== self::UNDEFINED) {
            $instance->brand = $brand;
        }
        if ($critical !== self::UNDEFINED) {
            $instance->critical = $critical;
        }
        if ($decorative !== self::UNDEFINED) {
            $instance->decorative = $decorative;
        }
        if ($info !== self::UNDEFINED) {
            $instance->info = $info;
        }
        if ($success !== self::UNDEFINED) {
            $instance->success = $success;
        }
        if ($warning !== self::UNDEFINED) {
            $instance->warning = $warning;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'accent' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'brand' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'critical' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'decorative' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'info' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'success' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'warning' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
