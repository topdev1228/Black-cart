<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|null $base
 * @property int|null $large
 * @property int|null $small
 */
class CheckoutBrandingCornerRadiusVariablesInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|null $base
     * @param int|null $large
     * @param int|null $small
     */
    public static function make(
        $base = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $large = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $small = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($base !== self::UNDEFINED) {
            $instance->base = $base;
        }
        if ($large !== self::UNDEFINED) {
            $instance->large = $large;
        }
        if ($small !== self::UNDEFINED) {
            $instance->small = $small;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'base' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'large' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'small' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
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
