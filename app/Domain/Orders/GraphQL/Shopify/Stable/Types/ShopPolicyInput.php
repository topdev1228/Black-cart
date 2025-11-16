<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $body
 * @property string $type
 */
class ShopPolicyInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $body
     * @param string $type
     */
    public static function make($body, $type): self
    {
        $instance = new self;

        if ($body !== self::UNDEFINED) {
            $instance->body = $body;
        }
        if ($type !== self::UNDEFINED) {
            $instance->type = $type;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'body' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
            'type' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
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
