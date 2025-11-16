<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property mixed|null $greaterThanOrEqualToSubtotal
 */
class DiscountMinimumSubtotalInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param mixed|null $greaterThanOrEqualToSubtotal
     */
    public static function make(
        $greaterThanOrEqualToSubtotal = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($greaterThanOrEqualToSubtotal !== self::UNDEFINED) {
            $instance->greaterThanOrEqualToSubtotal = $greaterThanOrEqualToSubtotal;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'greaterThanOrEqualToSubtotal' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
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
