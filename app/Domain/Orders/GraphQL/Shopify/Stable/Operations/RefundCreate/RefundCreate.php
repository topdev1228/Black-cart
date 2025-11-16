<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Operations\RefundCreate;

/**
 * @property string $__typename
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Operations\RefundCreate\RefundCreate\RefundCreatePayload|null $refundCreate
 */
class RefundCreate extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Operations\RefundCreate\RefundCreate\RefundCreatePayload|null $refundCreate
     */
    public static function make(
        $refundCreate = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        $instance->__typename = 'Mutation';
        if ($refundCreate !== self::UNDEFINED) {
            $instance->refundCreate = $refundCreate;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'refundCreate' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Operations\RefundCreate\RefundCreate\RefundCreatePayload),
        ];
    }

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../../sailor.php');
    }
}
