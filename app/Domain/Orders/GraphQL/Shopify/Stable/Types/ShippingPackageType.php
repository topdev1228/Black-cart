<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class ShippingPackageType
{
    public const BOX = 'BOX';
    public const ENVELOPE = 'ENVELOPE';
    public const FLAT_RATE = 'FLAT_RATE';
    public const SOFT_PACK = 'SOFT_PACK';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
