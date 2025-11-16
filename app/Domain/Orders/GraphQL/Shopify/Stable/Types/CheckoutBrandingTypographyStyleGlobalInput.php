<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string|null $kerning
 * @property string|null $letterCase
 */
class CheckoutBrandingTypographyStyleGlobalInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string|null $kerning
     * @param string|null $letterCase
     */
    public static function make(
        $kerning = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $letterCase = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($kerning !== self::UNDEFINED) {
            $instance->kerning = $kerning;
        }
        if ($letterCase !== self::UNDEFINED) {
            $instance->letterCase = $letterCase;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'kerning' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'letterCase' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
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
