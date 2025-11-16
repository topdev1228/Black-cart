<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property array<int|string> $mediaIds
 * @property int|string $variantId
 */
class ProductVariantAppendMediaInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param array<int|string> $mediaIds
     * @param int|string $variantId
     */
    public static function make($mediaIds, $variantId): self
    {
        $instance = new self;

        if ($mediaIds !== self::UNDEFINED) {
            $instance->mediaIds = $mediaIds;
        }
        if ($variantId !== self::UNDEFINED) {
            $instance->variantId = $variantId;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'mediaIds' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\ListConverter(new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter))),
            'variantId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
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
