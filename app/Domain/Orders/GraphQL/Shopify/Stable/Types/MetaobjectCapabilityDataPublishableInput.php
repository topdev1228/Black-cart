<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $status
 */
class MetaobjectCapabilityDataPublishableInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $status
     */
    public static function make($status): self
    {
        $instance = new self;

        if ($status !== self::UNDEFINED) {
            $instance->status = $status;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'status' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
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
