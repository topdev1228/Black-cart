<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property float|int|null $base
 * @property float|int|null $ratio
 */
class CheckoutBrandingFontSizeInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param float|int|null $base
     * @param float|int|null $ratio
     */
    public static function make(
        $base = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $ratio = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($base !== self::UNDEFINED) {
            $instance->base = $base;
        }
        if ($ratio !== self::UNDEFINED) {
            $instance->ratio = $ratio;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'base' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\FloatConverter),
            'ratio' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\FloatConverter),
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
