<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class CropRegion
{
    public const BOTTOM = 'BOTTOM';
    public const CENTER = 'CENTER';
    public const LEFT = 'LEFT';
    public const REGION = 'REGION';
    public const RIGHT = 'RIGHT';
    public const TOP = 'TOP';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
