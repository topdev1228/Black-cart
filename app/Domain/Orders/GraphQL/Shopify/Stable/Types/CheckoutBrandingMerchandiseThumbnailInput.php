<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string|null $border
 * @property string|null $cornerRadius
 */
class CheckoutBrandingMerchandiseThumbnailInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string|null $border
     * @param string|null $cornerRadius
     */
    public static function make(
        $border = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $cornerRadius = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($border !== self::UNDEFINED) {
            $instance->border = $border;
        }
        if ($cornerRadius !== self::UNDEFINED) {
            $instance->cornerRadius = $cornerRadius;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'border' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'cornerRadius' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
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
