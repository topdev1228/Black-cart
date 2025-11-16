<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string $parentId
 * @property \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MoneyInput $transactionAmount
 */
class ReturnRefundOrderTransactionInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string $parentId
     * @param \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MoneyInput $transactionAmount
     */
    public static function make($parentId, $transactionAmount): self
    {
        $instance = new self;

        if ($parentId !== self::UNDEFINED) {
            $instance->parentId = $parentId;
        }
        if ($transactionAmount !== self::UNDEFINED) {
            $instance->transactionAmount = $transactionAmount;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'parentId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'transactionAmount' => new \Spawnia\Sailor\Convert\NonNullConverter(new \App\Domain\Orders\GraphQL\Shopify\Stable\Types\MoneyInput),
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
