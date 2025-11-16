<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $code
 */
class DiscountRedeemCodeInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $code
     */
    public static function make($code): self
    {
        $instance = new self;

        if ($code !== self::UNDEFINED) {
            $instance->code = $code;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'code' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
