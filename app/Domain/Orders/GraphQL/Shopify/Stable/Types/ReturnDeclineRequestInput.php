<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property string $declineReason
 * @property int|string $id
 */
class ReturnDeclineRequestInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param string $declineReason
     * @param int|string $id
     */
    public static function make($declineReason, $id): self
    {
        $instance = new self;

        if ($declineReason !== self::UNDEFINED) {
            $instance->declineReason = $declineReason;
        }
        if ($id !== self::UNDEFINED) {
            $instance->id = $id;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'declineReason' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
            'id' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
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
