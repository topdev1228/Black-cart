<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property bool $active
 * @property string $name
 */
class DeliveryParticipantServiceInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param bool $active
     * @param string $name
     */
    public static function make($active, $name): self
    {
        $instance = new self;

        if ($active !== self::UNDEFINED) {
            $instance->active = $active;
        }
        if ($name !== self::UNDEFINED) {
            $instance->name = $name;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'active' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\BooleanConverter),
            'name' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\StringConverter),
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
