<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $value
 * @property string $valueType
 */
class PrivateMetafieldValueInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $value
     * @param string $valueType
     */
    public static function make($value, $valueType): self
    {
        $instance = new self;

        if ($value !== self::UNDEFINED) {
            $instance->value = $value;
        }
        if ($valueType !== self::UNDEFINED) {
            $instance->valueType = $valueType;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'value' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'valueType' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
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
