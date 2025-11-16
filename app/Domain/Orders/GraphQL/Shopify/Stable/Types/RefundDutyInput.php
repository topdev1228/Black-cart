<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string $dutyId
 * @property string|null $refundType
 */
class RefundDutyInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string $dutyId
     * @param string|null $refundType
     */
    public static function make(
        $dutyId,
        $refundType = 'Special default value that allows Sailor to differentiate between explicitly passing null and not passing a value at all.',
    ): self {
        $instance = new self;

        if ($dutyId !== self::UNDEFINED) {
            $instance->dutyId = $dutyId;
        }
        if ($refundType !== self::UNDEFINED) {
            $instance->refundType = $refundType;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'dutyId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'refundType' => new \Spawnia\Sailor\Convert\NullConverter(new \Spawnia\Sailor\Convert\EnumConverter),
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
