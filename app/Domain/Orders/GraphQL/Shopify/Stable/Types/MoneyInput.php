<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property mixed $amount
 * @property string $currencyCode
 */
class MoneyInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param mixed $amount
     * @param string $currencyCode
     */
    public static function make($amount, $currencyCode): self
    {
        $instance = new self;

        if ($amount !== self::UNDEFINED) {
            $instance->amount = $amount;
        }
        if ($currencyCode !== self::UNDEFINED) {
            $instance->currencyCode = $currencyCode;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'amount' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ScalarConverter),
            'currencyCode' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
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
