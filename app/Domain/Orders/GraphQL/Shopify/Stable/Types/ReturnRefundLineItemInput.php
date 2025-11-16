<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int $quantity
 * @property int|string $returnLineItemId
 */
class ReturnRefundLineItemInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int $quantity
     * @param int|string $returnLineItemId
     */
    public static function make($quantity, $returnLineItemId): self
    {
        $instance = new self;

        if ($quantity !== self::UNDEFINED) {
            $instance->quantity = $quantity;
        }
        if ($returnLineItemId !== self::UNDEFINED) {
            $instance->returnLineItemId = $returnLineItemId;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'quantity' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IntConverter),
            'returnLineItemId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
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
