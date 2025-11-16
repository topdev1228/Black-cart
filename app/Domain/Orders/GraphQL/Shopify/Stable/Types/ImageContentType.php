<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class ImageContentType
{
    public const BMP = 'BMP';
    public const JPG = 'JPG';
    public const PNG = 'PNG';
    public const WEBP = 'WEBP';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
