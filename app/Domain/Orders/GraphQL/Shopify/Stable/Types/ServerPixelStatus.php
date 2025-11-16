<?php declare(strict_types=1);

namespace App\Domain\Orders\GraphQL\Shopify\Stable\Types;

class ServerPixelStatus
{
    public const CONNECTED = 'CONNECTED';
    public const DISCONNECTED_CONFIGURED = 'DISCONNECTED_CONFIGURED';
    public const DISCONNECTED_UNCONFIGURED = 'DISCONNECTED_UNCONFIGURED';

    public static function endpoint(): string
    {
        return 'shopify-orders-2024-01';
    }

    public static function config(): string
    {
        return \Safe\realpath(__DIR__ . '/../../../../../../../sailor.php');
    }
}
