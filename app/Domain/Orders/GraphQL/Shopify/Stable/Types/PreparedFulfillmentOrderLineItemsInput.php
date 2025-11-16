<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string $fulfillmentOrderId
 */
class PreparedFulfillmentOrderLineItemsInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string $fulfillmentOrderId
     */
    public static function make($fulfillmentOrderId): self
    {
        $instance = new self;

        if ($fulfillmentOrderId !== self::UNDEFINED) {
            $instance->fulfillmentOrderId = $fulfillmentOrderId;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'fulfillmentOrderId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
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
