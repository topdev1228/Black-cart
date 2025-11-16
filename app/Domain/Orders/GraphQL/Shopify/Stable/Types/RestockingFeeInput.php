<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property float|int $percentage
 */
class RestockingFeeInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param float|int $percentage
     */
    public static function make($percentage): self
    {
        $instance = new self;

        if ($percentage !== self::UNDEFINED) {
            $instance->percentage = $percentage;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'percentage' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\FloatConverter),
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
