<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property mixed|null $greaterThanOrEqualToQuantity
 */
class DiscountMinimumQuantityInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param mixed|null $greaterThanOrEqualToQuantity
     */
    public static function make(
        $greaterThanOrEqualToQuantity = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($greaterThanOrEqualToQuantity !== self::UNDEFINED) {
            $instance->greaterThanOrEqualToQuantity = $greaterThanOrEqualToQuantity;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'greaterThanOrEqualToQuantity' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
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
