<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

/**
 * @property int|string $genericFileId
 * @property int $weight
 */
class CheckoutBrandingCustomFontInput extends \Spawnia\Sailor\ObjectLike
{
    /**
     * @param int|string $genericFileId
     * @param int $weight
     */
    public static function make($genericFileId, $weight): self
    {
        $instance = new self;

        if ($genericFileId !== self::UNDEFINED) {
            $instance->genericFileId = $genericFileId;
        }
        if ($weight !== self::UNDEFINED) {
            $instance->weight = $weight;
        }

        return $instance;
    }

    protected function converters(): array
    {
        static $converters;

        return $converters ??= [
            'genericFileId' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IDConverter),
            'weight' => new \Spawnia\Sailor\Convert\NonNullConverter(new \Spawnia\Sailor\Convert\IntConverter),
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
