<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string|null $border
 * @property string|null $color
 * @property string|null $cornerRadius
 * @property string|null $labelPosition
 */
class CheckoutBrandingControlInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string|null $border
     * @param string|null $color
     * @param string|null $cornerRadius
     * @param string|null $labelPosition
     */
    public static function make(
        $border = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $color = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $cornerRadius = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $labelPosition = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($border !== self::UNDEFINED) {
            $instance->border = $border;
        }
        if ($color !== self::UNDEFINED) {
            $instance->color = $color;
        }
        if ($cornerRadius !== self::UNDEFINED) {
            $instance->cornerRadius = $cornerRadius;
        }
        if ($labelPosition !== self::UNDEFINED) {
            $instance->labelPosition = $labelPosition;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'border' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'color' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'cornerRadius' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'labelPosition' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
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
