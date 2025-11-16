<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property bool $required
 * @property string $type
 */
class MarketplacePaymentsFeatureInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param bool $required
     * @param string $type
     */
    public static function make($required, $type): self
    {
        $instance = new self;

        if ($required !== self::UNDEFINED) {
            $instance->required = $required;
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
            'required' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
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
