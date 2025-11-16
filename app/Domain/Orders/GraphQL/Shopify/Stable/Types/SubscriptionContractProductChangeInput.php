<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property mixed|null $currentPrice
 * @property int|string|null $productVariantId
 */
class SubscriptionContractProductChangeInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param mixed|null $currentPrice
     * @param int|string|null $productVariantId
     */
    public static function make(
        $currentPrice = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
        $productVariantId = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($currentPrice !== self::UNDEFINED) {
            $instance->currentPrice = $currentPrice;
        }
        if ($productVariantId !== self::UNDEFINED) {
            $instance->productVariantId = $productVariantId;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'currentPrice' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'productVariantId' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\IDConverter),
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
