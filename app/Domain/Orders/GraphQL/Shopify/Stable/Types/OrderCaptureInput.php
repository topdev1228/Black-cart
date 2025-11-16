<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property mixed $amount
 * @property int|string $id
 * @property int|string $parentTransactionId
 * @property string|null $currency
 */
class OrderCaptureInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param mixed $amount
     * @param int|string $id
     * @param int|string $parentTransactionId
     * @param string|null $currency
     */
    public static function make(
        $amount,
        $id,
        $parentTransactionId,
        $currency = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($amount !== self::UNDEFINED) {
            $instance->amount = $amount;
        }
        if ($id !== self::UNDEFINED) {
            $instance->id = $id;
        }
        if ($parentTransactionId !== self::UNDEFINED) {
            $instance->parentTransactionId = $parentTransactionId;
        }
        if ($currency !== self::UNDEFINED) {
            $instance->currency = $currency;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'amount' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'id' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'parentTransactionId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'currency' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
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
