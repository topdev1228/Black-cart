<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string $fulfillmentOrderLineItemId
 * @property string|null $message
 */
class IncomingRequestLineItemInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string $fulfillmentOrderLineItemId
     * @param string|null $message
     */
    public static function make(
        $fulfillmentOrderLineItemId,
        $message = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($fulfillmentOrderLineItemId !== self::UNDEFINED) {
            $instance->fulfillmentOrderLineItemId = $fulfillmentOrderLineItemId;
        }
        if ($message !== self::UNDEFINED) {
            $instance->message = $message;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'fulfillmentOrderLineItemId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'message' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
