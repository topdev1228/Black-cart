<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string|null $calculation
 * @property mixed|null $price
 */
class PriceInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string|null $calculation
     * @param mixed|null $price
     */
    public static function make(
        $calculation = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $price = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($calculation !== self::UNDEFINED) {
            $instance->calculation = $calculation;
        }
        if ($price !== self::UNDEFINED) {
            $instance->price = $price;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'calculation' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'price' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
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
