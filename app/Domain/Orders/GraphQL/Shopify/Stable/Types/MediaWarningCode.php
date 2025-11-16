<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class MediaWarningCode
{
    public const MODEL_LARGE_PHYSICAL_SIZE = 'MODEL_LARGE_PHYSICAL_SIZE';
    public const MODEL_SMALL_PHYSICAL_SIZE = 'MODEL_SMALL_PHYSICAL_SIZE';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
