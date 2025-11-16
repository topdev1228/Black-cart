<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string|null $type
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SellingPlanCheckoutChargeValueInput|null $value
 */
class SellingPlanCheckoutChargeInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string|null $type
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SellingPlanCheckoutChargeValueInput|null $value
     */
    public static function make(
        $type = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $value = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($type !== self::UNDEFINED) {
            $instance->type = $type;
        }
        if ($value !== self::UNDEFINED) {
            $instance->value = $value;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'type' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'value' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\SellingPlanCheckoutChargeValueInput),
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
