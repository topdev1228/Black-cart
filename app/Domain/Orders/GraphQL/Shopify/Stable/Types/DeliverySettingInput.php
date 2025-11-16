<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property bool|null $legacyModeProfiles
 */
class DeliverySettingInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param bool|null $legacyModeProfiles
     */
    public static function make(
        $legacyModeProfiles = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($legacyModeProfiles !== self::UNDEFINED) {
            $instance->legacyModeProfiles = $legacyModeProfiles;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'legacyModeProfiles' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
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
