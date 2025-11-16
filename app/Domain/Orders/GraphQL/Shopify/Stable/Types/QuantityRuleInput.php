<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int $increment
 * @property int $minimum
 * @property int|string $variantId
 * @property int|null $maximum
 */
class QuantityRuleInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int $increment
     * @param int $minimum
     * @param int|string $variantId
     * @param int|null $maximum
     */
    public static function make(
        $increment,
        $minimum,
        $variantId,
        $maximum = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($increment !== self::UNDEFINED) {
            $instance->increment = $increment;
        }
        if ($minimum !== self::UNDEFINED) {
            $instance->minimum = $minimum;
        }
        if ($variantId !== self::UNDEFINED) {
            $instance->variantId = $variantId;
        }
        if ($maximum !== self::UNDEFINED) {
            $instance->maximum = $maximum;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'increment' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'minimum' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'variantId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'maximum' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IntConverter),
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
