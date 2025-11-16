<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class FileContentType
{
    public const EXTERNAL_VIDEO = 'EXTERNAL_VIDEO';
    public const FILE = 'FILE';
    public const IMAGE = 'IMAGE';
    public const MODEL_3D = 'MODEL_3D';
    public const VIDEO = 'VIDEO';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
