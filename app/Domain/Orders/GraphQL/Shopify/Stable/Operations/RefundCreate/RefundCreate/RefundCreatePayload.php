<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Operations\RefundCreate\RefundCreate;

/**
 * @property array<int, \App\Domain\Orders\GraphQL\Shopify\Stable\Operations\RefundCreate\RefundCreate\UserErrors\UserError> $userErrors
 * @property string $__typename
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Operations\RefundCreate\RefundCreate\Refund\Refund|null $refund
 */
class RefundCreatePayload extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param array<int, \App\Domain\Orders\GraphQL\Shopify\Stable\Operations\RefundCreate\RefundCreate\UserErrors\UserError> $userErrors
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Operations\RefundCreate\RefundCreate\Refund\Refund|null $refund
     */
    public static function make(
        $userErrors,
        $refund = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($userErrors !== self::UNDEFINED) {
            $instance->userErrors = $userErrors;
        }
        $instance->__typename = 'RefundCreatePayload';
        if ($refund !== self::UNDEFINED) {
            $instance->refund = $refund;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'userErrors' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Operations\RefundCreate\RefundCreate\UserErrors\UserError))),
            '__typename' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'refund' => new \Spawnia\Sailor\Convert\NullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Operations\RefundCreate\RefundCreate\Refund\Refund),
        ];
    }

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../../../sailor.php');
    }
}
