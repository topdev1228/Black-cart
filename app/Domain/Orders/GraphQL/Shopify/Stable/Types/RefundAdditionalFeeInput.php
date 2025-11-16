<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string $additionalFeeId
 */
class RefundAdditionalFeeInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string $additionalFeeId
     */
    public static function make($additionalFeeId): self
    {
        $instance = new self;

        if ($additionalFeeId !== self::UNDEFINED) {
            $instance->additionalFeeId = $additionalFeeId;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'additionalFeeId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
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
